<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;

$params = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'password' => $_POST['password']
];

$errors = [];

// validate form inputs

if ( !Validators::string(trim($params['name']), 3, 15) ) {
    $errors['name'] = "The name should be between 3 and 15 characters!";
}

if ( !Validators::string(trim($params['email']), 3, 15) ) {
    $errors['email'] = "Invalid email address";
}

if ( !Validators::string(trim($params['password']), 8, 20) ) {
    $errors['password'] = "The name should be between 8 and 20 characters!";
}

if (empty($errors)) {
    $query = App::resolve(DB::class);
    if ($query->executeQuery("select 1 from users where email = :email;", ['email' => $params['email']])->fetch()) {
        $errors['existance'] = "Account already exists";
    } else {
        $query->executeQuery("insert into users (name, email, password) values (:name, :email, :password);", [
            'name' => $params['name'],
            'email' => $params['email'],
            'password' => password_hash($params['password'], PASSWORD_BCRYPT)
        ]);


        header('location: /sign-in');
    }
    
}

base_path("controllers/register/index.php", ['errors' => $errors, 'params' => $params]);
    
