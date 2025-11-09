<?php

function uriIs ($uri) {
    return $_SERVER['REQUEST_URI'] === $uri;
}

function dd ($var) {
    var_dump($var);
    die();
}



function abort ($statusCode = 404) {
    http_response_code($statusCode);
    base_path("views/{$statusCode}.php");
} 

function base_path ($path, $attributes = []) {
    extract($attributes);
    require BASE_PATH . $path;
}

function login (string $username) {
    $_SESSION['user'] = $username;

    header('location: /');
    exit();
}

function logout () {
    $_SESSION = [];
    session_destroy();

    $session = session_get_cookie_params();
    setcookie('PHPSSID', '', time() - 3600, $session['path'], $session['domain'], $session['secure'], $session['httponly']);

    header('location: /');
    exit();
}