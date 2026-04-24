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

$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if ($password === '' || $confirm === '') {
    header('Location: admindashboard.php?page=setting&sub=delete&error=empty');
    exit;
}
if ($password !== $confirm) {
    header('Location: admindashboard.php?page=setting&sub=delete&error=mismatch');
    exit;
}

// verify password
$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($password, $hash)) {
    header('Location: admindashboard.php?page=setting&sub=delete&error=wrong');
    exit;
}

// delete user and logout
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->close();

session_destroy();
header('Location: index.php');
exit;
