<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\View;
use App\Models\Tag;

class TagController {
    public function index(): string {
        $tags = Tag::all();
        return View::render('tags.index', compact('tags'));
    }

    public function store(): void {
        $data = Request::validate([
            'name' => 'required|min:2|max:50',
            'color' => 'required',
        ]);

        $name = trim($data['name']);
        $color = trim($data['color']);

        // Check format of HEX color
        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            $color = '#3b82f6';
        }

        if (Tag::exists($name)) {
            Auth::init();
            $_SESSION['flash_errors']['name'][] = "A category or tag with this name already exists.";
            $_SESSION['flash_old'] = Request::all();
            header("Location: /tags");
            exit;
        }

        Tag::create([
            'name' => $name,
            'color' => $color,
        ]);

        Auth::init();
        $_SESSION['flash_success'] = "Category/Tag '{$name}' created successfully.";
        header("Location: /tags");
        exit;
    }

    public function update(string $id): void {
        $tagId = (int) $id;
        $tag = Tag::find($tagId);
        if (!$tag) {
            http_response_code(404);
            echo View::render('errors.404');
            exit;
        }

        $data = Request::validate([
            'name' => 'required|min:2|max:50',
            'color' => 'required',
        ]);

        $name = trim($data['name']);
        $color = trim($data['color']);

        if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            $color = $tag['color'];
        }

        if (Tag::exists($name, $tagId)) {
            Auth::init();
            $_SESSION['flash_errors']['name'][] = "A category or tag with this name already exists.";
            $_SESSION['flash_old'] = Request::all();
            header("Location: /tags");
            exit;
        }

        Tag::update($tagId, [
            'name' => $name,
            'color' => $color,
        ]);

        Auth::init();
        $_SESSION['flash_success'] = "Category/Tag updated successfully.";
        header("Location: /tags");
        exit;
    }

    public function destroy(string $id): void {
        $tagId = (int) $id;
        $tag = Tag::find($tagId);
        if (!$tag) {
            http_response_code(404);
            echo View::render('errors.404');
            exit;
        }

        Tag::delete($tagId);

        Auth::init();
        $_SESSION['flash_success'] = "Category/Tag deleted successfully.";
        header("Location: /tags");
        exit;
    }
}
