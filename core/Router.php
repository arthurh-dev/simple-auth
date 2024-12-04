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
    // Percorrer todas as rotas registradas
    foreach ($this->routes as $route => $action) {
        // Criar uma regex para capturar parâmetros dinâmicos, como {token}
        $routePattern = preg_replace('/{(\w+)}/', '(?P<$1>[^/]+)', $route);

        // Verificar se a URI corresponde à rota
        if (preg_match("#^$routePattern$#", $uri, $matches)) {
            // Separar controlador e método da rota
            list($controller, $method) = explode('@', $action);
            $controller = "App\\Controllers\\$controller";

            // Verificar se a classe e o método existem
            if (class_exists($controller) && method_exists($controller, $method)) {
                // Instanciar o controlador
                $instance = new $controller();

                // Captura os parâmetros e passa de forma correta
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)); // Pega apenas os parâmetros nomeados
                call_user_func_array([$instance, $method], $params); // Passar os parâmetros para o método

            } else {
                throw new \Exception("Controller or method not found: $controller@$method");
            }
            return;
        }
    }

    // Se não encontrar a rota, retornar 404
    http_response_code(404);
    echo "404 - Route not found.";
}
}
