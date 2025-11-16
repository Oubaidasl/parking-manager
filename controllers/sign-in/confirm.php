<?php

use Classes\Consts;
use Classes\DB;
use Classes\Validators;
use Classes\App;


$params = [
    'email' => $_POST['email'],
    'password' => $_POST['password']
];



$errors = [];

// validate form inputs

if ( !Validators::string(trim($params['email']), 3, 15) ) {
    $errors['email'] = "Invalid email address";
}

if ( !Validators::string(trim($params['password']), 8, 20) ) {
    $errors['password'] = "The password should be between 8 and 20 characters!";
}

if (empty($errors)) {
    $query = App::resolve(DB::class);
    $account = $query->executeQuery("select * from users where email = :email;", ['email' => $params['email']])->fetch(PDO::FETCH_ASSOC);
    if (! isset($account['email'])) {
        $errors['existance'] = "Account Does Not Exist";
    } else {
        if(! password_verify($params['password'], $account['password'])) {
            $errors['existance'] = "Incorrect Password";
        } else {
            login($account['name']);
            
        }
    }
    
}

base_path("controllers/sign-in/index.php", ['errors' => $errors, 'params' => $params]);
    
