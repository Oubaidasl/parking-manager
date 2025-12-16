<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

// SECURITY: API Key validation (ESP32 only)
$api_key = $_POST['key'] ?? '';
$valid_key = 'ESP32_RFID_2025_KEY123'; // Change this to your secret key

if ($api_key !== $valid_key) {
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'error' => 'Invalid API key'
    ]);
    exit;
}

$db = App::resolve(DB::class);

$db->executeQuery(
    "INSERT INTO `entrylog`(`client_nid`) VALUES (:client_nid)",
    ['client_nid' => $_POST['nid']]
);

// $rfid_list = [];
// foreach ($rows as $row) {
//     $rfid_list[] = [
//         'NID' => $row['nid'],
//         'RFID' => $row['badge_code']
//     ];
// }

http_response_code(200);
echo json_encode([
    'ok' => true,
]);
exit;