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

$old = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if ($old === '' || $new === '' || $confirm === '') {
    header('Location: admindashboard.php?page=setting&sub=password&error=empty');
    exit;
}
if ($new !== $confirm) {
    header('Location: admindashboard.php?page=setting&sub=password&error=mismatch');
    exit;
}

// check old password
$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($old, $hash)) {
    header('Location: admindashboard.php?page=setting&sub=password&error=wrong');
    exit;
}

$newhash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param('si', $newhash, $user_id);
$stmt->execute();
$stmt->close();

header('Location: admindashboard.php?page=setting&sub=password&msg=updated');
exit;
