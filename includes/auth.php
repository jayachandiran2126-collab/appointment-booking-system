<?php
// includes/auth.php
// Session helpers for user and admin authentication.

if (session_id() == '') {
    session_start();
}

/**
 * require_user() - redirect to login if user not authenticated
 */
function require_user() {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
        header('Location: ../public/index.php');
        exit;
    }
}

/**
 * require_admin() - redirect to admin login if not authenticated
 */
function require_admin() {
    if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
        header('Location: login.php');
        exit;
    }
}
?>
