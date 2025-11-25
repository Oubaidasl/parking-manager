<?php

header('Content-Type: application/json');


$clients = [
    'L679708' => [
        'NID' => 'L679708',
        'fullName' => 'Assladday Oubaida',
        'phoneNumber' => '0612777397',
        'email' => 'oassladday@gmail.com',
        'RFID' => '80:38104',
        'matricula' => 'A-44-56098',
        'endDate' => '2025-12-13'
    ],

    'L160997' => [
        'NID' => 'L160997',
        'fullName' => 'Assladday Mustaphpa',
        'phoneNumber' => '0668275899',
        'email' => '',
        'RFID' => '80:38555',
        'matricula' => 'A-40-50008',
        'endDate' => '2026-01-23'
    ]
];


// Read JSON body (NOT $_POST for application/json)
$data = json_decode($raw = file_get_contents('php://input'), true) ?? []; // associative array


$nid = $data['NID'] ?? null;
if (!$nid) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing NID']);
    exit;
}


// Example: fetch existing
$client = $clients[$nid] ?? null;
if (!$client) {
    http_response_code(200);
    echo json_encode([
        'ok' => false
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'ok' => true,
    'redirect' => '/client'
]);
exit;