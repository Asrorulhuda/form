<?php

namespace App\Core;

/**
 * Simple Router
 * Matches URL patterns to controller actions with parameter extraction.
 */
class Router
{
    private array $routes = [];
    private array $middlewareGroups = [];

    /**
     * Register a GET route
     */
    public function get(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $controller, $method, $middleware);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $controller, $method, $middleware);
    }

    /**
     * Register a PUT route (via POST with _method)
     */
    public function put(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('PUT', $path, $controller, $method, $middleware);
    }

    /**
     * Register a DELETE route (via POST with _method)
     */
    public function delete(string $path, string $controller, string $method, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $path, $controller, $method, $middleware);
    }

    /**
     * Add a route to the collection
     */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method, array $middleware): self
    {
        $this->routes[] = [
            'httpMethod'  => $httpMethod,
            'path'        => $path,
            'controller'  => $controller,
            'method'      => $method,
            'middleware'   => $middleware,
        ];
        return $this;
    }

    /**
     * Dispatch the request to the matching route
     */
    public function dispatch(string $url, string $httpMethod): void
    {
        $url = trim($url, '/');
        
        // Handle method spoofing for PUT/DELETE via POST
        if ($httpMethod === 'POST' && isset($_POST['_method'])) {
            $httpMethod = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            if ($route['httpMethod'] !== $httpMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $url, $matches)) {
                // Extract route parameters as sequential positional arguments for PHP 8 compatibility
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    $middlewareClass = "App\\Middleware\\{$mw}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            return;
                        }
                    }
                }

                // Instantiate controller and call method
                $controllerClass = "App\\Controllers\\{$route['controller']}";
                if (!class_exists($controllerClass)) {
                    $this->sendError(500, "Controller {$route['controller']} not found");
                    return;
                }

                $controller = new $controllerClass();
                $methodName = $route['method'];

                if (!method_exists($controller, $methodName)) {
                    $this->sendError(500, "Method {$methodName} not found in {$route['controller']}");
                    return;
                }

                call_user_func_array([$controller, $methodName], $params);
                return;
            }
        }

        // No route matched
        $this->sendError(404, 'Page not found');
    }

    /**
     * Convert route path to regex pattern
     * e.g., 'users/{id}' => '#^users/(?P<id>[^/]+)$#'
     */
    private function convertToRegex(string $path): string
    {
        $path = trim($path, '/');
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Send error response
     */
    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        $isDebug = (bool) env('APP_DEBUG', false);
        
        if (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
            str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')
        ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => ($code === 404) ? 'Endpoint tidak ditemukan.' : ($isDebug ? $message : 'Terjadi kesalahan sistem internal.')
            ]);
            return;
        }

        if ($code === 404) {
            if (file_exists(BASE_PATH . '/views/errors/404.php')) {
                include BASE_PATH . '/views/errors/404.php';
            } else {
                echo "<h1>404 - Halaman Tidak Ditemukan</h1><p>Halaman yang Anda cari tidak ditemukan.</p>";
            }
        } elseif ($code === 500) {
            if (file_exists(BASE_PATH . '/views/errors/500.php')) {
                $debug = $isDebug;
                include BASE_PATH . '/views/errors/500.php';
            } else {
                echo "<h1>500 - Kesalahan Internal</h1><p>" . ($isDebug ? htmlspecialchars($message) : 'Terjadi kesalahan pada server.') . "</p>";
            }
        } else {
            echo "<h1>Error {$code}</h1><p>" . ($isDebug ? htmlspecialchars($message) : 'Permintaan tidak dapat diproses.') . "</p>";
        }
    }

    /**
     * Generate URL for a route (simple version)
     */
    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim(env('APP_URL', ''), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}
