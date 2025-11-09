<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;

$error = ['body' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error['body'] = $errors['body'];
}

$query = App::resolve(DB::class);




base_path("views/notes/create.view.php", ['errors' => $error]);