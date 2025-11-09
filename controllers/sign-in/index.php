<?php

$error = [];
$param = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $errors;
    $param = $params;
}

base_path("views/sign-in/index.view.php", ['errors' => $error, 'params' => $param]);