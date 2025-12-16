<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

// SECURITY: API Key validation (ESP32 only)
$api_key = $_GET['key'] ?? '';
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

// Get all clients with RFID codes only
$rows = $db->executeQuery(
    "SELECT nid, badge_code FROM clients WHERE plan_end_date >= CURDATE() "
)->fetchAll(PDO::FETCH_ASSOC);

$rfid_list = [];
foreach ($rows as $row) {
    $rfid_list[] = [
        'NID' => $row['nid'],
        'RFID' => $row['badge_code']
    ];
}

http_response_code(200);
echo json_encode([
    'ok' => true,
    'rfids' => $rfid_list
]);
exit;