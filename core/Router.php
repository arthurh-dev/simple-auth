<?php
namespace Core;

class Router {
    private $routes = [];

    public function add($route, $handler) {
        $this->routes[$route] = $handler;
    }

    public function dispatch($uri) {
        foreach ($this->routes as $route => $handler) {
            if ($route === $uri) {
                list($controller, $method) = explode('@', $handler);
                $controller = "App\\Controllers\\$controller";
                if (class_exists($controller) && method_exists($controller, $method)) {
                    return (new $controller)->$method();
                }
            }
        }
        http_response_code(404);
        echo "404 Not Found";
    }
}
