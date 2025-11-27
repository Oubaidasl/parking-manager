<?php

use Core\Container;
use Core\App;
use Core\DB;

// Create and configure the container
$container = new Container();

// Bind DB::class to a resolver
$container->bind(DB::class, function () {
    $config = require BASE_PATH . "config.php";
    return new DB($config['parking_db']);
});

// Set container in App
App::setContainer($container);