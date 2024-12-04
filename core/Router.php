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

    // Despachar a rota
    public function dispatch($uri)
    {
        if (array_key_exists($uri, $this->routes)) {
            list($controller, $method) = explode('@', $this->routes[$uri]);
            $controller = "App\\Controllers\\$controller";
            
            if (class_exists($controller) && method_exists($controller, $method)) {
                $instance = new $controller();
                $instance->$method();
            } else {
                throw new \Exception("Controller or method not found: $controller@$method");
            }
        } else {
            http_response_code(404);
            echo "404 - Route not found.";
        }
    }
}
