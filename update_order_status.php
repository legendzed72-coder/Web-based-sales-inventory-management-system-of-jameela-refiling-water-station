<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admindashboard.php?page=pending');
    exit;
}
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($order_id > 0) {
    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE store_orders SET status='accepted' WHERE id=?");
        $stmt->bind_param('i', $order_id);
        if ($stmt->execute()) {
            $msg = 'order_accepted';
        }
        $stmt->close();
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE store_orders SET status='cancelled' WHERE id=?");
        $stmt->bind_param('i', $order_id);
        if ($stmt->execute()) {
            $msg = 'order_cancelled';
        }
        $stmt->close();
    }
}
header('Location: admindashboard.php?page=pending&msg=' . ($msg ?? 'updated'));
exit;
