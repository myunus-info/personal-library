<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            [$dsn, $user, $pass] = self::connectionConfig();

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function connectionConfig(?string $databaseUrl = null): array {
        $databaseUrl = $databaseUrl ?? self::env('DATABASE_URL', '');

        if ($databaseUrl !== '') {
            $parts = parse_url($databaseUrl);

            if ($parts === false) {
                die("Database connection failed: invalid DATABASE_URL");
            }

            $host = $parts['host'] ?? '127.0.0.1';
            $port = $parts['port'] ?? 5432;
            $db = isset($parts['path']) ? ltrim($parts['path'], '/') : 'library_db';
            $user = isset($parts['user']) ? urldecode($parts['user']) : '';
            $pass = isset($parts['pass']) ? urldecode($parts['pass']) : '';
            $query = [];

            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
            }

            $sslMode = $query['sslmode'] ?? 'require';
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslMode";

            return [$dsn, $user, $pass];
        }

        $host = self::env('DB_HOST', '127.0.0.1');
        $port = self::env('DB_PORT', '5432');
        $db   = self::env('DB_DATABASE', 'library_db');
        $user = self::env('DB_USERNAME', 'postgres');
        $pass = self::env('DB_PASSWORD', '');
        $sslMode = self::env('DB_SSLMODE', '');

        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        if ($sslMode !== '') {
            $dsn .= ";sslmode=$sslMode";
        }

        return [$dsn, $user, $pass];
    }

    private static function env(string $key, string $default = ''): string {
        $value = $_ENV[$key] ?? getenv($key);
        return $value === false || $value === null ? $default : $value;
    }
}
