<?php 

const BASE_PATH = __DIR__ . "/../";

ini_set('session.cookie_httponly', '1');        // JS cannot read the cookie 
ini_set('session.cookie_samesite', 'Lax');      // Mitigates CSRF for top-level navigations 
ini_set('session.use_strict_mode', '1');        // Prevents session fixation reuse 
// In production over HTTPS also set:
ini_set('session.cookie_secure', '1');          // Send cookie only over HTTPS 

session_start();

require BASE_PATH ."functions.php";

spl_autoload_register(function($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    require base_path($class . ".php");
});

require base_path("bootstrap.php");

require base_path('router.php');