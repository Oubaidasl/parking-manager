<?php

use Core\DB;
use Core\App;

date_default_timezone_set('UTC');

header('Content-Type: application/json');

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

$db = App::resolve(DB::class);
$client = $db->executeQuery(
    "SELECT * FROM clients WHERE nid = :nid",
    ['nid' => $nid]
)->fetch(PDO::FETCH_ASSOC);

// Example: fetch existing
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
    $currentEnd = isset($client['plan_end_date']) ? parseYmd($client['plan_end_date']) : $today;
    $base = ($currentEnd >= $today) ? $currentEnd : $today;
    $newEnd = addMonthsClamped($base, $months);
    // $client['endDate'] = formatYmd($newEnd);

    // 1) update client plan_end_date
    $db->executeQuery(
        "UPDATE clients 
         SET plan_end_date = :end_date 
         WHERE nid = :nid",
        [
            'end_date' => formatYmd($newEnd),
            'nid'      => $nid
        ]
    );

    // 2) log admin action: Renew
    $db->executeQuery(
        "INSERT INTO adminactions (admin_id, action_type, target_nid)
         VALUES (:admin_id, :action_type, :target_nid)",
        [
            'admin_id'   => $_SESSION['id'],
            'action_type'=> 'Renew',
            'target_nid' => $nid
        ]
    );

    // 3) log Renewals: Renew
    $db->executeQuery(
        "INSERT INTO renewals (admin_id, renewed_to, client_nid)
         VALUES (:admin_id, :renewed_to, :client_nid)",
        [
            'admin_id'   => $_SESSION['id'],
            'renewed_to' => formatYmd($newEnd),
            'client_nid' => $nid
        ]
    );


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

    // 1) update client plan_end_date
    $db->executeQuery(
        "UPDATE clients
            SET full_name = :full_name,
                phone     = :phone,
                email     = :email,
                badge_code = :rfid,
                vehicle_matricula = :matricula
            WHERE nid = :nid",
        [
            'full_name' => $data['fullName'],
            'phone'     => $data['phoneNumber'],
            'email'     => $data['email'] ?: null,
            'rfid'      => $data['RFID'],
            'matricula' => $data['matricula'] ?: null,
            'nid'       => $nid
        ]
    );

    // 2) log admin action: Update
    $db->executeQuery(
        "INSERT INTO adminactions (admin_id, action_type, target_nid)
         VALUES (:admin_id, :action_type, :target_nid)",
        [
            'admin_id'   => $_SESSION['id'],
            'action_type'=> 'Update',
            'target_nid' => $nid
        ]
    );

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

