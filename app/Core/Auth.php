<?php

namespace App\Core;

use PDO;

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::init();
        return isset($_SESSION['admin_user']);
    }

    public static function user(): ?array {
        self::init();
        return $_SESSION['admin_user'] ?? null;
    }

    public static function login(string $username, string $password): bool {
        self::init();
        
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];
            return true;
        }

        return false;
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['admin_user']);
        session_destroy();
    }
}
