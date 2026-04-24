<?php
/**
 * Logout for AQUAPAY
 * Destroys session and redirects to login page
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Destroy all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
