<?php

header('Content-Type: application/json');

http_response_code(200);

echo json_encode([
    'ok' => true,
    'totalSpots' => 5,
    'emptySpots' => 3,
    'totalUsers' => 255,
    'permitted' => 206,
    'expiredSubs' => 255-206,
    'expiringThisWeek' => 9,
    'newThisMonth' => 20
]);