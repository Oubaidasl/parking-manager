<?php 

use Core\Router;

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];



$router = new Router();

$router->get('/', 'controllers/home.php')->only('Auth');
$router->get('/login', 'controllers/session/index.php')->only('Guest');
$router->get('/login.js', 'views/login/login.js')->only('Guest');
$router->post('/login-create', 'controllers/session/create.php')->only('Guest');

$router->routeToController($uri, $method);