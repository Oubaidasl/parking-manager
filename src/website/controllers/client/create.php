<?php

use Core\DB;
use Core\App;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Read JSON body (NOT $_POST for application/json)
$data = json_decode(file_get_contents('php://input'), true) ?? []; // associative array

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

$db = App::resolve(DB::class);

$db->executeQuery(
    "INSERT INTO clients (nid, full_name, badge_code, phone, email, vehicle_matricula, admin_id) VALUES (:nid, :full_name, :badge_code, :phone, :email, :vehicle_matricula, :admin_id)",
    [
        'nid' => $data['NID'],
        'full_name' => $data['fullName'],
        'badge_code' => $data['RFID'],
        'phone' => $data['phoneNumber'],
        'email' => $data['email'] !== '' ? $data['email'] : null,
        'vehicle_matricula' => $data['matricula'] !== '' ? $data['matricula'] : null,
        'admin_id' => $_SESSION['id']
    ]
);

$db->executeQuery(
    "INSERT INTO adminactions (admin_id, action_type, target_nid) VALUES (:admin_id, :action_type, :target_nid)",
    [
        'admin_id' => $_SESSION['id'],
        'action_type' => 'Register',
        'target_nid' => $data['NID']
    ]
);

http_response_code(200);
echo json_encode([
    'ok' => true,
    'redirect' => '/client'
]);
exit;