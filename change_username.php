<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$current = isset($_POST['current_username']) ? trim($_POST['current_username']) : '';
$new = isset($_POST['new_username']) ? trim($_POST['new_username']) : '';

if ($current === '' || $new === '') {
    header('Location: admindashboard.php?page=setting&sub=username&error=empty');
    exit;
}

// validate current matches session
if ($current !== $_SESSION['username']) {
    header('Location: admindashboard.php?page=setting&sub=username&error=wrong');
    exit;
}

// update database
$stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
$stmt->bind_param('si', $new, $user_id);
$stmt->execute();
$stmt->close();

// update session
$_SESSION['username'] = $new;
header('Location: admindashboard.php?page=setting&sub=username&msg=updated');
exit;
