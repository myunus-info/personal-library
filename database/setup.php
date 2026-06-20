<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use Dotenv\Dotenv;

// Load env
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

$env = static function (string $key, string $default = ''): string {
    $value = $_ENV[$key] ?? getenv($key);
    return $value === false || $value === null ? $default : $value;
};

$databaseUrl = $env('DATABASE_URL');
$dbname = $env('DB_DATABASE', 'library_db');

if ($databaseUrl === '') {
    $host = $env('DB_HOST', '127.0.0.1');
    $port = $env('DB_PORT', '5432');
    $username = $env('DB_USERNAME', 'postgres');
    $password = $env('DB_PASSWORD');

    // Local PostgreSQL convenience: create the database if it does not exist.
    try {
        echo "Connecting to PostgreSQL server at $host:$port...\n";
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=postgres", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
        $stmt->execute(['dbname' => $dbname]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            echo "Database '$dbname' does not exist. Creating it...\n";
            $pdo->exec('CREATE DATABASE "' . str_replace('"', '""', $dbname) . '"');
            echo "Database '$dbname' created successfully.\n";
        } else {
            echo "Database '$dbname' already exists.\n";
        }
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage() . "\n");
    }
} else {
    $urlParts = parse_url($databaseUrl);
    $dbname = isset($urlParts['path']) ? ltrim($urlParts['path'], '/') : $dbname;
    echo "Using DATABASE_URL connection. Skipping database creation for remote PostgreSQL.\n";
}

// Now connect to the library database and run the schema
try {
    echo "Connecting to database '$dbname'...\n";
    [$dsn, $username, $password] = Database::connectionConfig();
    $db = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Running schema from database/schema.sql...\n";
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $db->exec($schema);
    echo "Schema executed successfully.\n";

    // Seed Admin User
    $adminUser = $env('ADMIN_USERNAME', 'admin');
    $adminPassword = $env('ADMIN_PASSWORD', 'password123');
    
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => $adminUser]);
    
    if ($stmt->fetchColumn() == 0) {
        echo "Seeding Admin user: $adminUser...\n";
        $hash = password_hash($adminPassword, PASSWORD_BCRYPT);
        $insertStmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
        $insertStmt->execute([
            'username' => $adminUser,
            'password_hash' => $hash,
        ]);
        echo "Admin user seeded successfully.\n";
    } else {
        echo "Admin user already exists.\n";
    }

    // Seed Default Tags
    $defaultTags = [
        ['Fiction', '#ec4899'],
        ['Non-Fiction', '#3b82f6'],
        ['Science', '#10b981'],
        ['History', '#f59e0b'],
        ['Biography', '#8b5cf6'],
        ['Mystery', '#ef4444'],
    ];

    $tagCheck = $db->prepare("SELECT COUNT(*) FROM tags WHERE LOWER(name) = LOWER(:name)");
    $tagInsert = $db->prepare("INSERT INTO tags (name, color) VALUES (:name, :color)");

    foreach ($defaultTags as $tag) {
        $tagCheck->execute(['name' => $tag[0]]);
        if ($tagCheck->fetchColumn() == 0) {
            echo "Seeding tag: {$tag[0]}...\n";
            $tagInsert->execute([
                'name' => $tag[0],
                'color' => $tag[1],
            ]);
        }
    }
    echo "Database seeding finished successfully!\n";

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
