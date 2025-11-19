<?php
// Start the session if not already active
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1) Clear server-side session data
$_SESSION = [];

// 2) Delete the session cookie in the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    // Use session_name() to get the actual cookie name (usually PHPSESSID)
    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => !empty($params['secure']),
            'httponly' => true,
            // If you set SameSite earlier, also include it here to match
            'samesite' => $params['samesite'] ?? 'Lax',
        ]
    );
}

// 3) Destroy the session on the server
session_destroy();

// 4) Redirect away from authenticated pages
header('Location: /login');
exit;
