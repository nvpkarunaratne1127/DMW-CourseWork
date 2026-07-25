<?php
// Include this file at the very top of any page that requires the librarian to be logged in.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
