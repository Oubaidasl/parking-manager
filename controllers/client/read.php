<?php

header('Content-Type: application/json');

$clients = [
    'L679708' => [
        'NID' => 'L679708',
        'fullName' => 'Assladday Oubaida',
        'RFID' => '80:38104',
        'matricula' => 'A-44-56098',
        'endDate' => '2025-12-13'
    ],

    'L160997' => [
        'NID' => 'L160997',
        'fullName' => 'Assladday Mustaphpa',
        'RFID' => '80:38555',
        'matricula' => 'A-40-50008',
        'endDate' => '2026-01-23'
    ]
];


http_response_code(200);
echo json_encode([
    'ok' => true,
    'clients' => $clients
]); 
exit;

