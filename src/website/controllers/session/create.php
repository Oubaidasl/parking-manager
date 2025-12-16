<?php 

use Core\App;
use Core\DB;

header('Content-Type: application/json');

// $creds = [
//     'oubaida' => 'oubaida_pass',
//     'omar' => 'omar_pass'
// ];


// Extract data and Decode into associative array
$data = json_decode(file_get_contents('php://input'), true);

// Guard: invalid/missing JSON
if ($data === null || !is_array($data)) {
  respond(400, false);
}

// Extract fields
$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

$db = App::resolve(DB::class);
$account = $db->executeQuery(
    "SELECT * FROM admins WHERE name = :username",
    ['username' => $username]
)->fetch(PDO::FETCH_ASSOC);


if (! $account) {
    respond(401, false, userErr: 'Invalid Username.');
}

if (! password_verify($password, $account['password'])) {
    respond(401, false, passErr: 'Invalid Password.');
}

session_regenerate_id(true);
$_SESSION['user'] = $account['name']; // or whole $account
$_SESSION['id'] = $account['id'];
respond(200, true);


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