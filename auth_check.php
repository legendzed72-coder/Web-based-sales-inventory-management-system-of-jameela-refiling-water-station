<?php
/**
 * Authentication Check
 * Include this file at the top of any page that requires login
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit;
}
?>
