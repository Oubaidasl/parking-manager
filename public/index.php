<?php 

const BASE_PATH = __DIR__ . "/../";

session_start();

require BASE_PATH ."functions.php";

spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path($class . ".php");
});

require base_path("bootstrap.php");

require base_path('router.php');