<?php

// Route: panel/login/auth_check.php

session_start();

$login_path = '../login/';

$timeout_duration = 3600;

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    header("Location: " . $login_path);
    exit;
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    
    $current_page = $_SERVER['REQUEST_URI'];

    session_unset();
    session_destroy();

    session_start();
    $_SESSION['redirect_url'] = $current_page;
    $_SESSION['flash_error'] = "نشست شما منقضی شد. لطفاً مجدداً وارد شوید.";

    header("Location: " . $login_path);
    exit;
}

$_SESSION['last_activity'] = time();

?>