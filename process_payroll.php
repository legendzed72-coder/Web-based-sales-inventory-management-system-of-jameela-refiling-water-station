<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// collect data
$name = isset($_POST['employee_name']) ? $conn->real_escape_string($_POST['employee_name']) : '';
$qty = isset($_POST['gallon_qty']) ? intval($_POST['gallon_qty']) : 0;
$commission = isset($_POST['commission_per']) ? floatval($_POST['commission_per']) : 0.0;
$total = isset($_POST['total_commission']) ? floatval($_POST['total_commission']) : $qty * $commission;


$stmt = $conn->prepare("INSERT INTO payroll_records (user_id, employee_name, gallon_qty, commission_per, total_commission) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('isidd', $_SESSION['user_id'], $name, $qty, $commission, $total);
$stmt->execute();
$stmt->close();

header('Location: admindashboard.php?page=payroll&msg=saved');
exit;
