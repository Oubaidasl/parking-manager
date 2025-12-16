<?php

use Core\DB;
use Core\App;

header('Content-Type: application/json');

$db = App::resolve(DB::class);

// ADMIN ACTIONS
$adminActionsRaw = $db->executeQuery("
    SELECT
        id,
        admin_id,
        action_type,
        target_nid,
        timestamp
    FROM adminactions
    ORDER BY timestamp DESC
")->fetchAll(PDO::FETCH_ASSOC);

$adminActions = array_map(function ($row) {
    return [
        'id'         => (int) $row['id'],
        'admin_id'   => (int) $row['admin_id'],
        'actionType' => $row['action_type'],
        'targetNid'  => $row['target_nid'],
        'timestamp'  => $row['timestamp'],
    ];
}, $adminActionsRaw);

// RENEWALS
$renewalsRaw = $db->executeQuery("
    SELECT
        id,
        client_nid,
        admin_id,
        renewed_to,
        timestamp
    FROM renewals
    ORDER BY timestamp DESC
")->fetchAll(PDO::FETCH_ASSOC);

$renewals = array_map(function ($row) {
    return [
        'id'        => (int) $row['id'],
        'clientNid' => $row['client_nid'],
        'admin_id'  => (int) $row['admin_id'],
        'renewedTo' => $row['renewed_to'],
        'timestamp' => $row['timestamp'],
    ];
}, $renewalsRaw);

// ENTRY LOGS
$entryLogsRaw = $db->executeQuery("
    SELECT
        id,
        client_nid,
        timestamp
    FROM entrylog
    ORDER BY timestamp DESC
")->fetchAll(PDO::FETCH_ASSOC);

$entryLogs = array_map(function ($row) {
    return [
        'id'        => (int) $row['id'],
        'clientNid' => $row['client_nid'],
        'timestamp' => $row['timestamp'],
    ];
}, $entryLogsRaw);

echo json_encode([
    'ok'          => true,
    'adminactions'=> $adminActions,
    'renewals'    => $renewals,
    'entrylogs'   => $entryLogs,
]);
exit;