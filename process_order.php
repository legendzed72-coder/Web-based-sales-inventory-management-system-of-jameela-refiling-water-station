<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

// ensure user logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// sanitize inputs
$name = isset($_POST['customer_name']) ? $conn->real_escape_string($_POST['customer_name']) : '';
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
$payment = isset($_POST['payment_method']) ? $conn->real_escape_string($_POST['payment_method']) : '';
$delivery = isset($_POST['delivery']) ? $conn->real_escape_string($_POST['delivery']) : 'pickup';
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // will be validated further


// begin transaction
$conn->begin_transaction();
$error_msg = '';
try {
    // validate inputs
    if (empty($name) || empty($address) || empty($payment)) {
        throw new Exception('All fields are required');
    }
    
    // insert order
    $stmt = $conn->prepare("INSERT INTO store_orders (user_id, customer_name, address, payment_method, delivery_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $user_id, $name, $address, $payment, $delivery);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    if ($product_id > 0 && $quantity > 0) {
        // fetch product price and update stock
        $stmt = $conn->prepare("SELECT price,stock FROM products WHERE id=?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stmt->bind_result($price,$stock);
        $stmt->fetch();
        $stmt->close();
        if ($stock >= $quantity) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiid', $order_id, $product_id, $quantity, $price);
            $stmt->execute();
            $stmt->close();
            // decrement stock
            $stmt = $conn->prepare("UPDATE products SET stock=stock-? WHERE id=?");
            $stmt->bind_param('ii', $quantity, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception('Insufficient stock');
        }
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $error_msg = $e->getMessage();
}

if ($error_msg) {
    header('Location: userdashboard.php?error=' . urlencode($error_msg));
} else {
    header('Location: userdashboard.php?msg=order_saved');
}
exit;
