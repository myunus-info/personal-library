<?php

namespace App\Core;

class Request {
    public static function all(): array {
        return array_merge($_GET, $_POST);
    }

    public static function input(string $key, $default = null) {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    public static function file(string $key): ?array {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK ? $_FILES[$key] : null;
    }

    public static function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function path(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return '/' . trim($path, '/');
    }

    public static function validate(array $rules): array {
        $errors = [];
        $data = self::all();
        $validated = [];

        foreach ($rules as $field => $rulesString) {
            $value = isset($data[$field]) ? trim($data[$field]) : '';
            $rulesList = explode('|', $rulesString);
            
            foreach ($rulesList as $rule) {
                if ($rule === 'required') {
                    // Check if value is empty or not set
                    if ($value === '') {
                        $errors[$field][] = "The " . str_replace('_', ' ', $field) . " field is required.";
                    }
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if ($value !== '' && strlen($value) < $min) {
                        $errors[$field][] = "The " . str_replace('_', ' ', $field) . " must be at least $min characters.";
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if ($value !== '' && strlen($value) > $max) {
                        $errors[$field][] = "The " . str_replace('_', ' ', $field) . " must not exceed $max characters.";
                    }
                } elseif ($rule === 'numeric') {
                    if ($value !== '' && !is_numeric($value)) {
                        $errors[$field][] = "The " . str_replace('_', ' ', $field) . " field must be a number.";
                    }
                } elseif ($rule === 'integer') {
                    if ($value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $errors[$field][] = "The " . str_replace('_', ' ', $field) . " field must be an integer.";
                    }
                }
            }
            $validated[$field] = $value;
        }

        if (!empty($errors)) {
            Auth::init();
            $_SESSION['flash_errors'] = $errors;
            $_SESSION['flash_old'] = $data;
            
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            header("Location: $referer");
            exit;
        }

        return $validated;
    }
}
