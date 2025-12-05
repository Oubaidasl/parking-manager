<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

$db = App::resolve(DB::class);

// slots
$slots = $db->executeQuery("
    SELECT
        COUNT(id) AS totalSlots,
        COUNT(CASE WHEN is_empty = 1 THEN 1 END) AS emptySlots
    FROM parkingslots
")->fetch(PDO::FETCH_ASSOC);

// clients
$clients = $db->executeQuery("
    SELECT
        COUNT(nid) AS totalUsers,
        COUNT(CASE WHEN DATE(plan_end_date) > CURDATE() THEN 1 END) AS permitted,
        COUNT(CASE WHEN plan_end_date >= CURDATE()
                   AND plan_end_date <  CURDATE() + INTERVAL 7 DAY THEN 1 END) AS expiringThisWeek,
        COUNT(CASE WHEN registration_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                   AND registration_date <  DATE_FORMAT(CURDATE() + INTERVAL 1 MONTH, '%Y-%m-01') THEN 1 END) AS newThisMonth
    FROM clients
")->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'ok'              => true,
    'totalSpots'      => $slots['totalSlots'],
    'emptySpots'      => $slots['emptySlots'],
    'totalUsers'      => $clients['totalUsers'],
    'permitted'       => $clients['permitted'],
    'expiredSubs'     => $clients['totalUsers'] - $clients['permitted'],
    'expiringThisWeek'=> $clients['expiringThisWeek'],
    'newThisMonth'    => $clients['newThisMonth'],
]);
