<?php

$error = [];
$param = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $errors;
    $param = $params;
}

base_path("views/register/index.view.php", ['errors' => $error, 'params' => $param]);