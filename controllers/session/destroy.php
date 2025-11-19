<?php 

$_SESSION = [];
session_destroy();

$session = session_get_cookie_params();
setcookie('PHPSSID', '', time() - 3600, $session['path'], $session['domain'], $session['secure'], $session['httponly']);

header('location: /');
exit();