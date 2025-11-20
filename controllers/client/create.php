<?php

header('Content-Type: application/json');

// Read JSON body (NOT $_POST for application/json)
$data = json_decode($raw = file_get_contents('php://input'), true) ?? []; // associative array

if (!validate_nid($data['NID']) || !validate_rfid($data['RFID']) || !validate_full_name($data['fullName']) || !validate_phone_number($data['phoneNumber']) || !validate_email($data['email']) || !validate_matricula($data['matricula'])) {
    http_response_code(200);
    echo json_encode([
        'ok' => false,
        'error' => [
            'nid' => !validate_nid($data['NID']),
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