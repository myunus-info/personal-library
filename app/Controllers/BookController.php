<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Models\Book;
use App\Models\Tag;

class BookController {
    public function index(): string {
        $filters = [
            'search' => Request::input('search'),
            'reading_status' => Request::input('reading_status'),
            'condition' => Request::input('condition'),
            'tag_id' => Request::input('tag_id'),
        ];

        $books = Book::all($filters);
        $stats = Book::getStats();
        $tags = Tag::all();

        return View::render('books.index', compact('books', 'stats', 'tags', 'filters'));
    }

    public function show(string $id): string {
        $bookId = (int) $id;
        $book = Book::find($bookId);
        if (!$book) {
            http_response_code(404);
            return View::render('errors.404');
        }

        return View::render('books.show', compact('book'));
    }

    public function create(): string {
        $tags = Tag::all();
        return View::render('books.create', compact('tags'));
    }

    public function store(): void {
        $data = Request::validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'max:20',
            'description' => 'max:5000',
            'condition' => 'required',
            'reading_status' => 'required',
            'current_page' => 'required|integer',
            'total_pages' => 'required|integer',
        ]);

        $tagIds = Request::input('tags', []);
        
        // Handle cover upload
        $coverPath = null;
        $file = Request::file('cover_image');
        if ($file) {
            $coverPath = $this->handleUpload($file);
        }

        $data['cover_image'] = $coverPath;

        // Auto-correct page parameters
        $currentPage = (int) $data['current_page'];
        $totalPages = (int) $data['total_pages'];
        if ($currentPage < 0) {
            $currentPage = 0;
        }
        if ($totalPages < 0) {
            $totalPages = 0;
        }
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $data['current_page'] = $currentPage;
        $data['total_pages'] = $totalPages;

        // Adjust reading status automatically
        if ($currentPage > 0) {
            if ($totalPages > 0 && $currentPage >= $totalPages) {
                $data['reading_status'] = 'completed';
            } else {
                $data['reading_status'] = 'reading';
            }
        } else {
            $data['reading_status'] = 'to_read';
        }

        $bookId = Book::create($data, $tagIds);

        Auth::init();
        $_SESSION['flash_success'] = "Book '{$data['title']}' added successfully!";
        header("Location: /books/{$bookId}");
        exit;
    }

    public function edit(string $id): string {
        $bookId = (int) $id;
        $book = Book::find($bookId);
        if (!$book) {
            http_response_code(404);
            return View::render('errors.404');
        }

        $tags = Tag::all();
        $bookTagIds = array_column($book['tags'], 'id');

        return View::render('books.edit', compact('book', 'tags', 'bookTagIds'));
    }

    public function update(string $id): void {
        $bookId = (int) $id;
        $book = Book::find($bookId);
        if (!$book) {
            http_response_code(404);
            echo View::render('errors.404');
            exit;
        }

        $data = Request::validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'max:20',
            'description' => 'max:5000',
            'condition' => 'required',
            'reading_status' => 'required',
            'current_page' => 'required|integer',
            'total_pages' => 'required|integer',
        ]);

        $tagIds = Request::input('tags', []);
        
        $data['existing_cover_image'] = $book['cover_image'];
        $file = Request::file('cover_image');
        if ($file) {
            // Delete old file if exists
            if (!empty($book['cover_image'])) {
                $oldFile = dirname(__DIR__, 2) . '/public' . $book['cover_image'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            $data['cover_image'] = $this->handleUpload($file);
        }

        // Auto-correct page parameters
        $currentPage = (int) $data['current_page'];
        $totalPages = (int) $data['total_pages'];
        if ($currentPage < 0) {
            $currentPage = 0;
        }
        if ($totalPages < 0) {
            $totalPages = 0;
        }
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        $data['current_page'] = $currentPage;
        $data['total_pages'] = $totalPages;

        // Adjust reading status automatically
        if ($currentPage > 0) {
            if ($totalPages > 0 && $currentPage >= $totalPages) {
                $data['reading_status'] = 'completed';
            } else {
                $data['reading_status'] = 'reading';
            }
        } else {
            $data['reading_status'] = 'to_read';
        }

        Book::update($bookId, $data, $tagIds);

        Auth::init();
        $_SESSION['flash_success'] = "Book updated successfully!";
        header("Location: /books/{$bookId}");
        exit;
    }

    public function updateProgress(string $id): void {
        $bookId = (int) $id;
        $book = Book::find($bookId);
        if (!$book) {
            http_response_code(404);
            echo View::render('errors.404');
            exit;
        }

        $data = Request::validate([
            'current_page' => 'required|integer',
        ]);

        Book::updateProgress($bookId, (int) $data['current_page']);

        Auth::init();
        $_SESSION['flash_success'] = "Reading progress updated!";
        header("Location: /books/{$bookId}");
        exit;
    }

    public function destroy(string $id): void {
        $bookId = (int) $id;
        $book = Book::find($bookId);
        if (!$book) {
            http_response_code(404);
            echo View::render('errors.404');
            exit;
        }

        // Delete cover image file if exists
        if (!empty($book['cover_image'])) {
            $filePath = dirname(__DIR__, 2) . '/public' . $book['cover_image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        Book::delete($bookId);

        Auth::init();
        $_SESSION['flash_success'] = "Book deleted successfully.";
        header("Location: /");
        exit;
    }

    private function handleUpload(array $file): string {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            Auth::init();
            $_SESSION['flash_errors']['cover_image'][] = "Only JPG, PNG, and WebP images are allowed.";
            $_SESSION['flash_old'] = Request::all();
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        if ($file['size'] > $maxSize) {
            Auth::init();
            $_SESSION['flash_errors']['cover_image'][] = "The image size must not exceed 2MB.";
            $_SESSION['flash_old'] = Request::all();
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/covers';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('cover_', true) . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/uploads/covers/' . $filename;
        }

        Auth::init();
        $_SESSION['flash_errors']['cover_image'][] = "Failed to upload image. Please try again.";
        $_SESSION['flash_old'] = Request::all();
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}
