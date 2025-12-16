<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

$db = App::resolve(DB::class);

// adjust column names to your table
$rows = $db->executeQuery(
    "SELECT nid, full_name, phone, email, badge_code, vehicle_matricula, plan_end_date FROM clients"
)->fetchAll(PDO::FETCH_ASSOC);

$clients = [];
foreach ($rows as $row) {
    $nid = $row['nid'];

    $clients[$nid] = [
        'NID'         => $row['nid'],
        'fullName'    => $row['full_name'],
        'phoneNumber' => $row['phone'],
        'email'       => $row['email'] ?? '',
        'RFID'        => $row['badge_code'],
        'matricula'   => $row['vehicle_matricula'] ?? '',
        'endDate'     => $row['plan_end_date'], // or whatever column name
    ];
}

http_response_code(200);
echo json_encode([
    'ok' => true,
    'clients' => $clients
]); 
exit;

