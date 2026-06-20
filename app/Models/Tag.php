<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Tag {
    public static function all(): array {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM tags ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM tags WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $tag = $stmt->fetch();
        return $tag ?: null;
    }

    public static function exists(string $name, ?int $exceptId = null): bool {
        $db = Database::getConnection();
        if ($exceptId !== null) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM tags WHERE LOWER(name) = LOWER(:name) AND id != :exceptId");
            $stmt->execute(['name' => trim($name), 'exceptId' => $exceptId]);
        } else {
            $stmt = $db->prepare("SELECT COUNT(*) FROM tags WHERE LOWER(name) = LOWER(:name)");
            $stmt->execute(['name' => trim($name)]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function create(array $data): int {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO tags (name, color) VALUES (:name, :color)");
        $stmt->execute([
            'name' => trim($data['name']),
            'color' => trim($data['color'] ?? '#3b82f6'),
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE tags SET name = :name, color = :color WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'name' => trim($data['name']),
            'color' => trim($data['color']),
        ]);
    }

    public static function delete(int $id): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM tags WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getForBook(int $bookId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT t.* FROM tags t
            JOIN book_tags bt ON t.id = bt.tag_id
            WHERE bt.book_id = :book_id
            ORDER BY t.name ASC
        ");
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetchAll();
    }
}
