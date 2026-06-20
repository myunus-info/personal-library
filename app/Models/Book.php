<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Book {
    public static function all(array $filters = []): array {
        $db = Database::getConnection();
        
        $sql = "SELECT b.* FROM books b WHERE 1=1";
        $params = [];

        // Apply Search Filter (Title, Author, ISBN)
        if (!empty($filters['search'])) {
            $sql .= " AND (b.title ILIKE :search OR b.author ILIKE :search OR b.isbn ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Apply Reading Status Filter
        if (!empty($filters['reading_status'])) {
            $sql .= " AND b.reading_status = :reading_status";
            $params['reading_status'] = $filters['reading_status'];
        }

        // Apply Physical Condition Filter
        if (!empty($filters['condition'])) {
            $sql .= " AND b.condition = :condition";
            $params['condition'] = $filters['condition'];
        }

        // Apply Tag Filter (requires subquery or JOIN)
        if (!empty($filters['tag_id'])) {
            $sql .= " AND b.id IN (SELECT book_id FROM book_tags WHERE tag_id = :tag_id)";
            $params['tag_id'] = (int) $filters['tag_id'];
        }

        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $books = $stmt->fetchAll();

        if (empty($books)) {
            return [];
        }

        // Map tags to books to avoid N+1 query problem
        $bookIds = array_column($books, 'id');
        $inClause = implode(',', array_fill(0, count($bookIds), '?'));
        
        $tagStmt = $db->prepare("
            SELECT bt.book_id, t.id, t.name, t.color 
            FROM tags t
            JOIN book_tags bt ON t.id = bt.tag_id
            WHERE bt.book_id IN ($inClause)
            ORDER BY t.name ASC
        ");
        $tagStmt->execute($bookIds);
        $tagsByBook = [];
        
        while ($row = $tagStmt->fetch()) {
            $bookId = $row['book_id'];
            unset($row['book_id']);
            $tagsByBook[$bookId][] = $row;
        }

        foreach ($books as &$book) {
            $book['tags'] = $tagsByBook[$book['id']] ?? [];
            // Calculate progress percentage on the fly to support PHP level accuracy
            $book['progress_percent'] = $book['total_pages'] > 0 
                ? (int) round(($book['current_page'] * 100) / $book['total_pages']) 
                : 0;
        }

        return $books;
    }

    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch();

        if (!$book) {
            return null;
        }

        $book['tags'] = Tag::getForBook($id);
        $book['progress_percent'] = $book['total_pages'] > 0 
            ? (int) round(($book['current_page'] * 100) / $book['total_pages']) 
            : 0;

        return $book;
    }

    public static function create(array $data, array $tagIds = []): int {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("
                INSERT INTO books (title, author, isbn, description, cover_image, condition, reading_status, current_page, total_pages)
                VALUES (:title, :author, :isbn, :description, :cover_image, :condition, :reading_status, :current_page, :total_pages)
            ");
            
            $stmt->execute([
                'title' => trim($data['title']),
                'author' => trim($data['author']),
                'isbn' => !empty($data['isbn']) ? trim($data['isbn']) : null,
                'description' => !empty($data['description']) ? trim($data['description']) : null,
                'cover_image' => !empty($data['cover_image']) ? trim($data['cover_image']) : null,
                'condition' => $data['condition'] ?? 'new',
                'reading_status' => $data['reading_status'] ?? 'to_read',
                'current_page' => (int) ($data['current_page'] ?? 0),
                'total_pages' => (int) ($data['total_pages'] ?? 0),
            ]);

            $bookId = (int) $db->lastInsertId();

            if (!empty($tagIds)) {
                $tagStmt = $db->prepare("INSERT INTO book_tags (book_id, tag_id) VALUES (:book_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $tagStmt->execute([
                        'book_id' => $bookId,
                        'tag_id' => (int) $tagId,
                    ]);
                }
            }

            $db->commit();
            return $bookId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, array $data, array $tagIds = []): bool {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare("
                UPDATE books 
                SET title = :title, author = :author, isbn = :isbn, description = :description, 
                    cover_image = :cover_image, condition = :condition, reading_status = :reading_status, 
                    current_page = :current_page, total_pages = :total_pages, updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            
            $params = [
                'id' => $id,
                'title' => trim($data['title']),
                'author' => trim($data['author']),
                'isbn' => !empty($data['isbn']) ? trim($data['isbn']) : null,
                'description' => !empty($data['description']) ? trim($data['description']) : null,
                'cover_image' => !empty($data['cover_image']) ? trim($data['cover_image']) : $data['existing_cover_image'] ?? null,
                'condition' => $data['condition'] ?? 'new',
                'reading_status' => $data['reading_status'] ?? 'to_read',
                'current_page' => (int) ($data['current_page'] ?? 0),
                'total_pages' => (int) ($data['total_pages'] ?? 0),
            ];

            $stmt->execute($params);

            // Sync tags
            $deleteStmt = $db->prepare("DELETE FROM book_tags WHERE book_id = :book_id");
            $deleteStmt->execute(['book_id' => $id]);

            if (!empty($tagIds)) {
                $tagStmt = $db->prepare("INSERT INTO book_tags (book_id, tag_id) VALUES (:book_id, :tag_id)");
                foreach ($tagIds as $tagId) {
                    $tagStmt->execute([
                        'book_id' => $id,
                        'tag_id' => (int) $tagId,
                    ]);
                }
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateProgress(int $id, int $currentPage): bool {
        $db = Database::getConnection();
        
        // Fetch book first to check total pages
        $book = self::find($id);
        if (!$book) {
            return false;
        }

        $totalPages = (int) $book['total_pages'];
        
        // Ensure current page is bounded
        if ($currentPage < 0) {
            $currentPage = 0;
        }
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        // Determine Reading Status automatically
        $status = 'to_read';
        if ($currentPage > 0) {
            if ($totalPages > 0 && $currentPage >= $totalPages) {
                $status = 'completed';
            } else {
                $status = 'reading';
            }
        }

        $stmt = $db->prepare("
            UPDATE books 
            SET current_page = :current_page, reading_status = :reading_status, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'current_page' => $currentPage,
            'reading_status' => $status,
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM books WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getStats(): array {
        $db = Database::getConnection();
        
        $total = (int) $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
        $toRead = (int) $db->query("SELECT COUNT(*) FROM books WHERE reading_status = 'to_read'")->fetchColumn();
        $reading = (int) $db->query("SELECT COUNT(*) FROM books WHERE reading_status = 'reading'")->fetchColumn();
        $completed = (int) $db->query("SELECT COUNT(*) FROM books WHERE reading_status = 'completed'")->fetchColumn();
        
        $new = (int) $db->query("SELECT COUNT(*) FROM books WHERE condition = 'new'")->fetchColumn();
        $good = (int) $db->query("SELECT COUNT(*) FROM books WHERE condition = 'good'")->fetchColumn();
        $damaged = (int) $db->query("SELECT COUNT(*) FROM books WHERE condition = 'damaged'")->fetchColumn();
        
        return [
            'total' => $total,
            'to_read' => $toRead,
            'reading' => $reading,
            'completed' => $completed,
            'new' => $new,
            'good' => $good,
            'damaged' => $damaged,
        ];
    }
}
