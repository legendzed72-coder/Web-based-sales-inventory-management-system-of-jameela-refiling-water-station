<?php
/**
 * Database Configuration for AQUAPAY
 * XAMPP MySQL default credentials
 */

$db_host = 'localhost';
$db_name = 'aquapay';
$db_user = 'root';
$db_pass = '';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Function to get database connection
function getDB() {
    global $conn;
    return $conn;
}

// automatically ensure important tables exist and seed minimal data
function initializeDatabase() {
    global $conn;
    
    // Ensure uploads directory exists
    $uploads_dir = __DIR__ . '/uploads/products';
    if (!is_dir($uploads_dir)) {
        @mkdir($uploads_dir, 0755, true);
    }
    
    // users table with default admin
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        full_name VARCHAR(100),
        role ENUM('admin','user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    $res = $conn->query("SELECT COUNT(*) as cnt FROM users");
    if ($res) {
        $row = $res->fetch_assoc();
        if ($row['cnt'] == 0) {
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (username,password,role,full_name,email) VALUES ('admin','$admin_password','admin','Administrator','admin@aquapay.com')");
        }
    }

    // products table with sample entries
    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        stock INT DEFAULT 0,
        image VARCHAR(255),
        category VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    $res = $conn->query("SELECT COUNT(*) as cnt FROM products");
    if ($res) {
        $row = $res->fetch_assoc();
        if ($row['cnt'] == 0) {
            $conn->query("INSERT INTO products (name, description, price, stock, category) VALUES
            ('Water Jug 20L','Refillable water jug',20.00,100,'containers'),
            ('Water Jug 10L','Medium water jug',15.00,150,'containers'),
            ('Water Jug 5L','Small water jug',10.00,200,'containers'),
            ('Distilled Water 1L','1 Liter distilled water',5.00,500,'bottles'),
            ('Purified Water 1L','1 Liter purified water',3.00,500,'bottles'),
            ('Gallon Cap','Gallon cap replacement',2.00,300,'accessories'),
            ('Water Dispenser','Electric water dispenser',150.00,50,'equipment'),
            ('Water Filter','Replacement water filter',25.00,100,'accessories')
            ");
        }
    }

    // store orders and items
    $conn->query("CREATE TABLE IF NOT EXISTS store_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        address VARCHAR(255),
        payment_method VARCHAR(100),
        delivery_type ENUM('pickup','deliver') DEFAULT 'pickup',
        status ENUM('pending','accepted','completed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    $conn->query("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES store_orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");

    // payroll records
    $conn->query("CREATE TABLE IF NOT EXISTS payroll_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        employee_name VARCHAR(100) NOT NULL,
        gallon_qty INT DEFAULT 0,
        commission_per DECIMAL(10,2) DEFAULT 0,
        total_commission DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
}

initializeDatabase();
?>
