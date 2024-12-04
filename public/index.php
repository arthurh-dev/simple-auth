<?php
require_once '../config/config.php';
require_once '../vendor/autoload.php';

use Core\Router;

$router = new Router();

// Defina as rotas
$router->add('/', 'HomeController@index');
$router->add('/login', 'AuthController@login');
$router->add('/register', 'AuthController@register');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($uri);
