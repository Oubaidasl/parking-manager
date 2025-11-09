<?php

use Classes\Router;

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];



$router = new Router();

$router->get('/', 'controllers/home.php');
$router->get('/calendar', 'controllers/calendar.php');
$router->get('/dashboard', 'controllers/dashboard.php');
$router->get('/projects', 'controllers/projects.php');
$router->get('/notes', 'controllers/notes/index.php')->only("Auth");
$router->get('/note', 'controllers/notes/show.php');
$router->delete('/note-delete', 'controllers/notes/destroy.php');
$router->get('/note-create', 'controllers/notes/create.php');
$router->post('/note-store', 'controllers/notes/store.php');
$router->get('/note-edit', 'controllers/notes/edit.php');
$router->patch('/note-update', 'controllers/notes/update.php');
$router->get('/register', 'controllers/register/index.php')->only("Guest");
$router->post('/register-store', 'controllers/register/store.php');
$router->get('/sign-in', 'controllers/sign-in/index.php')->only("Guest");
$router->post('/sign-in-confirm', 'controllers/sign-in/confirm.php');
$router->delete('/sign-out', 'controllers/sign-in/destroy.php')->only("Auth");



$router->routeToController($uri, $method);