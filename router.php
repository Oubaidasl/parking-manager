<?php 

use Core\Router;

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];



$router = new Router();

$router->get('/', 'controllers/home.php')->only('Guest');
$router->get('/login', 'controllers/login/create.php');

$router->routeToController($uri, $method);