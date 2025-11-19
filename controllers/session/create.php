<?php 

header('Content-Type: application/json');

$creds = [
    'oubaida' => 'oubaida_pass',
    'omar' => 'omar_pass'
];

// Extract data and Decode into associative array
$data = json_decode(file_get_contents('php://input'), true);

// Guard: invalid/missing JSON
if ($data === null || !is_array($data)) {
  respond(400, false);
}

// Extract fields
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

foreach ($creds as $user => $pass) {
    if ($username === $user) {
        if ($password === $pass) {
            $_SESSION['user'] = $user;
            respond(200, true);
        }
        respond(401, false, passErr:'Invalid Password.');
    }
}
respond(401, false, userErr:'Invalid Username.');

function respond (int $responseCode, bool $ok, string $redirect = '/', string $userErr = '', string $passErr = '') {
    http_response_code($responseCode);
    echo json_encode([
        'ok' => $ok,
        'redirect' => $redirect,
        'error' => [
            'username' => $userErr,
            'password' => $passErr
            ]
        ]); 
    exit;
}