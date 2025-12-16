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

// Update each parking spot status
for ($i = 1; $i <= 5; $i++) {
    $spotKey = 'spot' . $i;
    if (isset($_POST[$spotKey])) {
        $isEmpty = $_POST[$spotKey] == '1' ? 1 : 0;
        
        $db->executeQuery(
            "UPDATE parkingslots SET is_empty = :is_empty WHERE id = :id",
            ['is_empty' => $isEmpty, 'id' => $i]
        );
    }
}

http_response_code(200);
echo json_encode([
    'ok' => true,
]);
exit;