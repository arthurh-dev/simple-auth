<?php
require_once '../config/config.php';
require_once '../vendor/autoload.php';

use Core\Router;

$router = new Router();

// Definir as rotas
$router->add('/', 'HomeController@index');
$router->add('/register', 'AuthController@register');

// Obter a URI atual e despachar para o roteador
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($uri);
