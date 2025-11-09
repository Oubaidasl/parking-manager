<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;



base_path("Classes/Validators.php");




$errors = ['body' => ''];
$query = App::resolve(DB::class);


if ( !Validators::string(trim($_POST['note']), max:1000) ) {
    $errors['body'] = "The body should not be null or more than 1000 characters!";
}

if (empty($errors['body'])) {
    $query->executeQuery("UPDATE notes SET note = :note WHERE id = :id;", [
        'note' => $_POST['note'],
        'id' => $_POST['id']
    ]);

    header('location: /notes');
    exit();
}

base_path("controllers/notes/edit.php", ['errors' => $errors]);
    
