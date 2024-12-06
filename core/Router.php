<?php

namespace Core;

class Router
{
    protected $routes = [];

    // Adicionar uma rota ao roteador
    public function add($route, $action)
    {
        $this->routes[$route] = $action;
    }

public function dispatch($uri)
{
    foreach ($this->routes as $route => $action) {
        $routePattern = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $route);

        if (preg_match("#^$routePattern$#", $uri, $matches)) {
            list($controller, $method) = explode('@', $action);
            $controller = "App\\Controllers\\$controller";

            if (class_exists($controller) && method_exists($controller, $method)) {
                $instance = new $controller();
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)); // Pega apenas os parâmetros nomeados
                call_user_func_array([$instance, $method], $params); // Passar os parâmetros para o método

            } else {
                throw new \Exception("Controller or method not found: $controller@$method");
            }
            return;
        }
    }

    http_response_code(404);
    echo "404 - Route not found.";
}
}
