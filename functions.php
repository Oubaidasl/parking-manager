<?php 

function base_path ($path = '') {
    return BASE_PATH . $path;
}

function dd($var) {
    var_dump($var);
    die();
}

function abort ($statusCode = 404) {
    http_response_code($statusCode);
    require base_path("views/{$statusCode}.html");
} 