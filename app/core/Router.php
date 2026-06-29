<?php
// app/core/Router.php

/**
 * Router – maps URL paths to Controller@method.
 *
 * Route definition examples:
 *   $router->get('/',                  'AuthController@showLogin');
 *   $router->post('/login',            'AuthController@login');
 *   $router->get('/lecturer/sessions', 'LecturerController@sessions', ['auth','role:lecturer']);
 */
class Router
{
    private array $routes = [];

    // ── Route registration ────────────────────────────────────────────────────

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, string $handler, array $middleware): void
    {
        // Convert :param wildcards to named capture groups
        $pattern = preg_replace('/\/:([a-zA-Z_]+)/', '/(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = compact('method', 'path', 'pattern', 'handler', 'middleware');
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────

    public function dispatch(): void
{
    $method     = $_SERVER['REQUEST_METHOD'];
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?');

    // Strip subfolder prefix e.g. /attendance_system/public
    $scriptDir  = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $basePath   = rtrim($scriptDir, '/');
    $uri        = '/' . ltrim(substr($requestUri, strlen($basePath)), '/');

    // Remove index.php from URI if accessed directly
    $uri = preg_replace('#^/index\.php(/|$)#', '/', $uri);
    if (empty($uri)) $uri = '/';

    foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            // Named URL params (e.g. :id)
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            // Run middleware stack
            foreach ($route['middleware'] as $mw) {
                $this->runMiddleware($mw, $params);
            }

            // Resolve Controller@method
            [$class, $action] = explode('@', $route['handler']);

            $controllerFile = APP_PATH . "/controllers/{$class}.php";
            if (!file_exists($controllerFile)) {
                $this->abort(500, "Controller {$class} not found.");
                return;
            }

            require_once $controllerFile;
            $controller = new $class();

            if (!method_exists($controller, $action)) {
                $this->abort(500, "Method {$action} not found in {$class}.");
                return;
            }

            $controller->$action($params);
            return;
        }

        // No route matched
        $this->abort(404, 'Page not found.');
    }

    // ── Middleware runner ─────────────────────────────────────────────────────

    private function runMiddleware(string $mw, array $params): void
    {
        if ($mw === 'auth') {
            require_once APP_PATH . '/middleware/AuthMiddleware.php';
            AuthMiddleware::handle();
            return;
        }

        if (str_starts_with($mw, 'role:')) {
            $role = substr($mw, 5);
            require_once APP_PATH . '/middleware/RoleMiddleware.php';
            RoleMiddleware::handle($role);
            return;
        }
    }

    // ── Error pages ───────────────────────────────────────────────────────────

    private function abort(int $code, string $message): void
    {
        http_response_code($code);
        echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
    }
}
