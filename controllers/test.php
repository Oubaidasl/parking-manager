<?php

use Core\DB;
use Core\App;

// $username = 'oubaida';

// $db = App::resolve(DB::class);
// $account = $db->executeQuery(
//     "SELECT * FROM admins WHERE name = :username",
//     ['username' => $username]
// )->fetch(PDO::FETCH_ASSOC);

// $data = [
//     'NID'         => 'L160997',
//     'RFID'        => '90:99:AF:00',
//     'fullName'    => 'Assladday Mustaphpa',
//     'phoneNumber' => '0544444444',
//     'email'       => NULL,
//     'matricula'   => NULL
// ];


// $db->executeQuery(
//     "INSERT INTO clients (nid, full_name, badge_code, phone, email, vehicle_matricula, admin_id) VALUES (:nid, :full_name, :badge_code, :phone, :email, :vehicle_matricula, :admin_id)",
//     [
//         'nid' => $data['NID'],
//         'full_name' => $data['fullName'],
//         'badge_code' => $data['RFID'],
//         'phone' => $data['phoneNumber'],
//         'email' => $data['email'] ?? null,
//         'vehicle_matricula' => $data['matricula'] ?? null,
//         'admin_id' => $_SESSION['id']
//     ]
// );

// $db->executeQuery(
//     "INSERT INTO adminactions (admin_id, action_type, target_nid) VALUES (:admin_id, :action_type, :target_nid)",
//     [
//         'admin_id' => $_SESSION['id'],
//         'action_type' => 'Register',
//         'target_nid' => $data['NID']
//     ]
// );

$db = App::resolve(DB::class);

// Get all clients with RFID codes only
$rows = $db->executeQuery(
    "SELECT COUNT(`id`) AS count FROM `parkingslots` WHERE `is_empty`"
)->fetch();

echo($rows['count']);

var_dump($rows);

// var_dump($_SESSION);