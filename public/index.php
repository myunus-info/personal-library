<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Auth;
use App\Core\Router;
use Dotenv\Dotenv;

// Initialize session
Auth::init();

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Enable error reporting in development
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Router Setup
$router = new Router();

// Guest Routes
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin'], 'guest');
$router->post('/login', [\App\Controllers\AuthController::class, 'login'], 'guest');

// Authenticated Routes
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout'], 'auth');
$router->get('/', [\App\Controllers\BookController::class, 'index'], 'auth');
$router->get('/books/create', [\App\Controllers\BookController::class, 'create'], 'auth');
$router->post('/books', [\App\Controllers\BookController::class, 'store'], 'auth');
$router->get('/books/{id}', [\App\Controllers\BookController::class, 'show'], 'auth');
$router->get('/books/{id}/edit', [\App\Controllers\BookController::class, 'edit'], 'auth');
$router->post('/books/{id}', [\App\Controllers\BookController::class, 'update'], 'auth');
$router->post('/books/{id}/progress', [\App\Controllers\BookController::class, 'updateProgress'], 'auth');
$router->post('/books/{id}/delete', [\App\Controllers\BookController::class, 'destroy'], 'auth');

// Tag Routes
$router->get('/tags', [\App\Controllers\TagController::class, 'index'], 'auth');
$router->post('/tags', [\App\Controllers\TagController::class, 'store'], 'auth');
$router->post('/tags/{id}', [\App\Controllers\TagController::class, 'update'], 'auth');
$router->post('/tags/{id}/delete', [\App\Controllers\TagController::class, 'destroy'], 'auth');

// Dispatch Router
$router->dispatch();
