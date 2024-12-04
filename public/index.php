<?php
require_once '../config/config.php';
require_once '../vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Core\Router;

$router = new Router();

// Definir as rotas
$router->add('/', 'HomeController@index');
$router->add('/register', 'AuthController@register');

// Obter a URI atual
$basePath = '/simple-auth'; // Subpasta do projeto
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remover o prefixo da URI
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

// Despachar para o roteador
$router->dispatch($uri);
