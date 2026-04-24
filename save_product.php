<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admindashboard.php?page=products');
    exit;
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
$description = isset($_POST['description']) ? $conn->real_escape_string(trim($_POST['description'])) : '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
$category = isset($_POST['category']) ? $conn->real_escape_string(trim($_POST['category'])) : '';

// Validate inputs
if (empty($name) || $price < 0 || $stock < 0) {
    header('Location: admindashboard.php?page=products&error=Invalid input');
    exit;
}

// Handle image upload
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploads_dir = __DIR__ . '/uploads/products';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            header('Location: admindashboard.php?page=products&action=' . ($product_id ? 'edit' : 'add') . '&error=Invalid image format');
            exit;
        }
        
        // Generate filename
        $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_path = 'uploads/products/' . $filename;
        $full_path = $uploads_dir . '/' . $filename;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
            header('Location: admindashboard.php?page=products&action=' . ($product_id ? 'edit' : 'add') . '&error=Failed to upload image');
            exit;
        }
    } else {
        header('Location: admindashboard.php?page=products&action=' . ($product_id ? 'edit' : 'add') . '&error=Upload error');
        exit;
    }
}

// Save to database
if ($product_id > 0) {
    // Update existing product
    if ($image_path) {
        // Delete old image if exists
        $result = $conn->query("SELECT image FROM products WHERE id=$product_id");
        if ($result) {
            $old_product = $result->fetch_assoc();
            if ($old_product['image'] && file_exists(__DIR__ . '/' . $old_product['image'])) {
                unlink(__DIR__ . '/' . $old_product['image']);
            }
        }
        
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=?, image=? WHERE id=?");
        $stmt->bind_param('ssdiisi', $name, $description, $price, $stock, $category, $image_path, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, category=? WHERE id=?");
        $stmt->bind_param('ssdis', $name, $description, $price, $stock, $category, $product_id);
    }
} else {
    // Insert new product
    if (!$image_path) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdis', $name, $description, $price, $stock, $category);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdiss', $name, $description, $price, $stock, $category, $image_path);
    }
}

if ($stmt->execute()) {
    $stmt->close();
    header('Location: admindashboard.php?page=products&msg=' . urlencode($product_id ? 'Product updated successfully' : 'Product added successfully'));
} else {
    header('Location: admindashboard.php?page=products&error=' . urlencode('Failed to save product: ' . $conn->error));
}

$conn->close();
exit;
