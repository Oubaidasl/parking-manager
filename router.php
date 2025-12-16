<?php 

use Core\Router;

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];



$router = new Router();

$router->get('/', 'controllers/dashboard/index.php')->only('Auth');
$router->get('/login', 'controllers/session/update.php')->only('Guest');
$router->get('/login.js', 'views/login/login.js')->only('Guest');
$router->post('/session-create', 'controllers/session/create.php')->only('Guest');
$router->get('/session-destroy', 'controllers/session/destroy.php')->only('Auth');
$router->get('/images/1.jpg', 'public/images/1.jpg')->only('Auth');
$router->get('/client', 'controllers/client/index.php')->only('Auth');
$router->get('/client-read.js', 'views/client/read.js')->only('Auth');
$router->get('/client-read', 'controllers/client/read.php')->only('Auth');
$router->get('/client-create.js', 'views/client/create.js')->only('Auth');
$router->post('/client-create', 'controllers/client/create.php')->only('Auth');
$router->get('/client-renew.js', 'views/client/renew.js')->only('Auth');
$router->patch('/client-update', 'controllers/client/update.php')->only('Auth');
$router->get('/client-edit.js', 'views/client/edit.js')->only('Auth');
$router->delete('/client-delete', 'controllers/client/destroy.php')->only('Auth');
$router->get('/client-delete.js', 'views/client/delete.js')->only('Auth');
$router->get('/dashboard-read.js', 'views/dashboard/read.js')->only('Auth');
$router->get('/dashboard-read', 'controllers/dashboard/read.php')->only('Auth');
$router->get('/log', 'controllers/log/index.php')->only('Auth');
$router->get('/log-read', 'controllers/log/read.php')->only('Auth');
$router->get('/log-read.js', 'views/log/read.js')->only('Auth');
$router->get('/esp-read', 'controllers/esp/read.php');
$router->post('/esp-create', 'controllers/esp/create.php');
$router->post('/esp-update', 'controllers/esp/update.php');
$router->get('/test', 'controllers/test.php');


$router->routeToController($uri, $method);