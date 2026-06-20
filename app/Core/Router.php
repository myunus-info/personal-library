<?php

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler, string $middleware = ''): void {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, string $middleware = ''): void {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, string $middleware): void {
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void {
        $requestPath = Request::path();
        $requestMethod = Request::method();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert route path to regex (e.g., /books/{id} -> ^/books/([a-zA-Z0-9_]+)$)
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['path']);
            $pattern = '@^' . $pattern . '$@';

            if (preg_match($pattern, $requestPath, $matches)) {
                array_shift($matches); // Remove the full match

                // Handle middleware
                if ($route['middleware'] === 'auth' && !Auth::check()) {
                    Auth::init();
                    $_SESSION['flash_error'] = "Please log in to access the application.";
                    header("Location: /login");
                    exit;
                }

                if ($route['middleware'] === 'guest' && Auth::check()) {
                    header("Location: /");
                    exit;
                }

                [$controllerClass, $methodName] = $route['handler'];
                
                if (!class_exists($controllerClass)) {
                    die("Controller class $controllerClass does not exist");
                }
                
                $controller = new $controllerClass();
                
                if (!method_exists($controller, $methodName)) {
                    die("Method $methodName does not exist on controller $controllerClass");
                }
                
                // Call method with parameters and capture output
                $response = call_user_func_array([$controller, $methodName], $matches);
                
                if (is_string($response)) {
                    echo $response;
                }
                return;
            }
        }

        // Handle 404
        http_response_code(404);
        try {
            echo View::render('errors.404');
        } catch (\Exception $e) {
            echo "<h1>404 Not Found</h1><p>The page you requested was not found.</p>";
        }
    }
}
