<?php
// Only run this check on protected pages, not on login/logout
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin_id is not set, redirect to login
if (!isset($_SESSION['admin_id'])) {
    // Prevent redirect loop: only redirect if not already on login.php
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        header('Location: login.php');
        exit;
    }
}

