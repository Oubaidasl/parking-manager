<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');


// Read JSON body (NOT $_POST for application/json)
$data = json_decode($raw = file_get_contents('php://input'), true) ?? []; // associative array


$nid = $data['NID'] ?? null;
if (!$nid) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing NID']);
    exit;
}

$db = App::resolve(DB::class);
$client = $db->executeQuery(
    "SELECT * FROM clients WHERE nid = :nid",
    ['nid' => $nid]
)->fetch(PDO::FETCH_ASSOC);


// Example: fetch existing
if (!$client) {
    http_response_code(200);
    echo json_encode([
        'ok' => false
    ]);
    exit;
}

$db->executeQuery(
    "DELETE FROM clients WHERE nid = :nid",
    ['nid' => $nid]   // e.g. $nid = 'MA123456789'
);

$db->executeQuery(
    "INSERT INTO adminactions (admin_id, action_type, target_nid)
    VALUES (:admin_id, :action_type, :target_nid)",
    [
        'admin_id'   => $_SESSION['id'],
        'action_type'=> 'Delete',
        'target_nid' => $nid
    ]
);

http_response_code(200);
echo json_encode([
    'ok' => true,
    'redirect' => '/client'
]);
exit;