<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;

class AuthController {
    public function showLogin(): string {
        if (Auth::check()) {
            header("Location: /");
            exit;
        }
        return View::render('auth.login');
    }

    public function login(): void {
        $data = Request::validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::login($data['username'], $data['password'])) {
            Auth::init();
            $_SESSION['flash_success'] = "Welcome back, Admin!";
            header("Location: /");
            exit;
        }

        Auth::init();
        $_SESSION['flash_error'] = "Invalid username or password.";
        $_SESSION['flash_old'] = ['username' => $data['username']];
        header("Location: /login");
        exit;
    }

    public function logout(): void {
        Auth::logout();
        Auth::init();
        $_SESSION['flash_success'] = "You have been logged out successfully.";
        header("Location: /login");
        exit;
    }
}
