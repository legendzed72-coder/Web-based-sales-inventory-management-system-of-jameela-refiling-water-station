<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id > 0) {
    // Get product to delete image if exists
    $result = $conn->query("SELECT image FROM products WHERE id=$product_id");
    if ($result) {
        $product = $result->fetch_assoc();
        if ($product['image'] && file_exists(__DIR__ . '/' . $product['image'])) {
            unlink(__DIR__ . '/' . $product['image']);
        }
    }
    
    // Delete product
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();
    
    header('Location: admindashboard.php?page=products&msg=' . urlencode('Product deleted successfully'));
} else {
    header('Location: admindashboard.php?page=products&error=' . urlencode('Invalid product ID'));
}

$conn->close();
exit;
