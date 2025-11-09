<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;




$errors = ['body' => ''];
$query = App::resolve(DB::class);


if ( !Validators::string(trim($_POST['note']), max:1000) ) {
    $errors['body'] = "The body should not be null or more than 1000 characters!";
}

if (empty($errors['body'])) {
    $query->executeQuery("insert into notes (note, owner_id) values (:note, :owner);", [
        'note' => $_POST['note'],
        'owner' => Consts::OWNER_ID
    ]);

    header('location: /notes');
    exit();
}

base_path("controllers/notes/create.php", ['errors' => $errors]);
    
