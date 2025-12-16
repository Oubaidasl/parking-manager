<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

$db = App::resolve(DB::class);

// slots
$slotsRaw = $db->executeQuery("
    SELECT
        id,
        is_empty
    FROM parkingslots
")->fetchAll(PDO::FETCH_ASSOC);

// totals
$totalSlots = count($slotsRaw);
$emptySlots = 0;

$slotsData = array_map(function ($row) use (&$emptySlots) {
    if ($row['is_empty']) {
        $emptySlots++;
    }

    return [
        'id'     => (int) $row['id'],
        'label'  => 'P' . (int) $row['id'],           // <- use id as name
        'status' => $row['is_empty'] ? 'EMPTY' : 'OCCUPIED',
    ];
}, $slotsRaw);

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
    'ok'               => true,
    'totalSpots'       => $totalSlots,
    'emptySpots'       => $emptySlots,
    'totalUsers'       => (int) $clients['totalUsers'],
    'permitted'        => (int) $clients['permitted'],
    'expiredSubs'      => (int) $clients['totalUsers'] - (int) $clients['permitted'],
    'expiringThisWeek' => (int) $clients['expiringThisWeek'],
    'newThisMonth'     => (int) $clients['newThisMonth'],
    'slots'            => $slotsData,
]);