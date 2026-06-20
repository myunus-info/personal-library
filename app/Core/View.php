<?php

namespace App\Core;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class View {
    private static ?Factory $factory = null;

    public static function boot(): void {
        if (self::$factory !== null) {
            return;
        }

        $container = new Container();
        $filesystem = new Filesystem();

        $viewPaths = [dirname(__DIR__, 2) . '/views'];
        $cachePath = dirname(__DIR__, 2) . '/cache';

        if (!$filesystem->isDirectory($cachePath)) {
            $filesystem->makeDirectory($cachePath, 0755, true);
        }

        $finder = new FileViewFinder($filesystem, $viewPaths);
        $resolver = new EngineResolver();

        $bladeCompiler = new BladeCompiler($filesystem, $cachePath);

        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });

        $resolver->register('php', function () {
            return new \Illuminate\View\Engines\PhpEngine();
        });

        $dispatcher = new Dispatcher($container);
        self::$factory = new Factory($resolver, $finder, $dispatcher);
        self::$factory->setContainer($container);
    }

    public static function render(string $view, array $data = []): string {
        self::boot();
        
        // Share auth status globally
        $data['admin'] = Auth::user();
        
        // Share alerts globally
        $data['success'] = $_SESSION['flash_success'] ?? null;
        $data['error'] = $_SESSION['flash_error'] ?? null;
        $data['errors'] = $_SESSION['flash_errors'] ?? [];
        $data['old'] = $_SESSION['flash_old'] ?? [];
        
        // Clear flash data so it only lasts for one request
        unset($_SESSION['flash_success']);
        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_errors']);
        unset($_SESSION['flash_old']);
        
        return self::$factory->make($view, $data)->render();
    }
}
