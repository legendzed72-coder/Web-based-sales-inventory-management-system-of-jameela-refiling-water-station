<?php
/**
 * Database Setup Script for AQUAPAY
 * Run this file once to set up the database and tables
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

// Connect without selecting database first
$conn = new mysqli($db_host, $db_user, $db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS aquapay";
if ($conn->query($sql) === TRUE) {
    echo "Database 'aquapay' created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db('aquapay');

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'products' created successfully<br>";
} else {
    echo "Error creating products table: " . $conn->error . "<br>";
}

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'orders' created successfully<br>";
} else {
    echo "Error creating orders table: " . $conn->error . "<br>";
}

// Create order_items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'order_items' created successfully<br>";
} else {
    echo "Error creating order_items table: " . $conn->error . "<br>";
}

// Insert sample products (check if empty first)
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $sql = "INSERT INTO products (name, description, price, stock, category) VALUES
    ('Water Jug 20L', 'Refillable water jug', 20.00, 100, 'containers'),
    ('Water Jug 10L', 'Medium water jug', 15.00, 150, 'containers'),
    ('Water Jug 5L', 'Small water jug', 10.00, 200, 'containers'),
    ('Distilled Water 1L', '1 Liter distilled water', 5.00, 500, 'bottles'),
    ('Purified Water 1L', '1 Liter purified water', 3.00, 500, 'bottles'),
    ('Gallon Cap', 'Gallon cap replacement', 2.00, 300, 'accessories'),
    ('Water Dispenser', 'Electric water dispenser', 150.00, 50, 'equipment'),
    ('Water Filter', 'Replacement water filter', 25.00, 100, 'accessories')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Sample products inserted successfully<br>";
    } else {
        echo "Error inserting products: " . $conn->error . "<br>";
    }
}

// Insert default admin user (check if empty first)
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Password is 'admin123' hashed with password_hash()
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password, email, full_name, role) VALUES
    ('admin', '$admin_password', 'admin@aquapay.com', 'Administrator', 'admin')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Default admin user created (username: admin, password: admin123)<br>";
    } else {
        echo "Error inserting admin: " . $conn->error . "<br>";
    }
}

echo "<h2>Database setup complete!</h2>";
echo "<p>You can now <a href='login.php'>login</a> with the following credentials:</p>";
echo "<ul><li>Admin: username: <strong>admin</strong>, password: <strong>admin123</strong></li></ul>";

$conn->close();
?>
