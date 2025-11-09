<?php

use Classes\Container;
use Classes\App;
use Classes\DB;

// Create and configure the container
$container = new Container();

// Bind DB::class to a resolver
$container->bind(DB::class, function () {
    $config = require BASE_PATH . "config.php";
    return new DB($config['notes_db']);
});

// Set container in App
App::setContainer($container);
