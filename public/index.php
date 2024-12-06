<?php
require_once '../config/config.php';
require_once '../vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Core\Router;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$router = new Router();

// Definir as rotas
$router->add('/', 'HomeController@index');
$router->add('/register', 'AuthController@register');
$router->add('/login', 'AuthController@login');
$router->add('/confirm/{token}', 'AuthController@confirm');
$router->add('/dashboard', 'DashboardController@index');
$router->add('/logout', 'AuthController@logout');



$basePath = '/simple-auth'; // Subpasta do projeto
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

$router->dispatch($uri);
