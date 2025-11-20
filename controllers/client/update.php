<?php

date_default_timezone_set('UTC');

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

// Quick presence checks
$hasPlan = array_key_exists('plan', $data);         // renewal path 
$hasProfile = array_key_exists('fullName', $data)   // profile path (example fields)
            && array_key_exists('phoneNumber', $data)
            && array_key_exists('RFID', $data); // adjust to your schema 

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
        'ok' => false,
        'error' => [
            'nid' => true
        ]
    ]);
    exit;
}

if ($hasPlan) {

    $months = (int)$data['plan']; // expects 1,3,6,12

    if ($months < 1) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid plan']);
        exit;
    }

    $today = new DateTimeImmutable('today'); 
    $currentEnd = isset($client['endDate']) ? parseYmd($client['endDate']) : $today;
    $base = ($currentEnd >= $today) ? $currentEnd : $today;
    $newEnd = addMonthsClamped($base, $months);
    $client['endDate'] = formatYmd($newEnd);

    http_response_code(200);
    echo json_encode([
        'ok' => true,
        'redirect' => '/client'
    ]); 
    exit;

} elseif ($hasProfile) {

    if (!validate_rfid($data['RFID']) || !validate_full_name($data['fullName']) || !validate_phone_number($data['phoneNumber']) || !validate_email($data['email']) || !validate_matricula($data['matricula'])) {
        http_response_code(200);
        echo json_encode([
            'ok' => false,
            'error' => [
                'rfid' => !validate_rfid($data['RFID']),
                'fullName' => !validate_full_name($data['fullName']),
                'phoneNumber' => !validate_phone_number($data['phoneNumber']),
                'email' => $data['email'] ? !validate_email($data['email']) : null,
                'matricula' => $data['matricula'] ? !validate_matricula($data['matricula']) : null
            ]
        ]); 
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'ok' => true,
        'redirect' => '/client'
    ]);
    exit;

} else {
    // If neither branch matched, report bad request
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No actionable fields']);
    exit;
}

