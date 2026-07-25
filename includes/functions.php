<?php
/**
 * Small helper functions shared across the site.
 */

// Store a one-time success/error message to show after a redirect
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type;   // 'success' or 'danger'
    $_SESSION['flash_msg']  = $message;
}

// Print the flash message (if any) as a Bootstrap alert, then clear it
function show_flash() {
    if (!empty($_SESSION['flash_msg'])) {
        $type = htmlspecialchars($_SESSION['flash_type']);
        $msg  = htmlspecialchars($_SESSION['flash_msg']);
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $msg
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['flash_type'], $_SESSION['flash_msg']);
    }
}

// Shortcut to escape output safely
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
