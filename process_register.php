<?php
/**
 * Process Registration for AQUAPAY
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

$error = '';
$success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data and sanitize
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username already exists. Please choose another.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error registering user. Please try again.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AQUAPAY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-image: url(79a6727f-7028-42fd-bc1b-b0afb91c1870.jpg); background-size: cover; min-height: 600px;">

    <div class="navbar" style="padding: 15px;">
      <h1 class="logo">AQUAPAY</h1>
      <nav id="nav">
        <ul>
          <li><a href="index.php">HOME</a></li>
          <li><a href="index.php#about">ABOUT US</a></li>
          <li><a href="index.php#contact">CONTACT US</a></li>
        </ul>
      </nav>
    </div>

    <div id="login-container">
        <?php if ($error): ?>
            <p style="color: red; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p style="color: green; margin-bottom: 15px;"><?= $success ?></p>
        <?php else: ?>
            <h2>Register</h2>
            <form id="login-box" action="process_register.php" method="POST">
                <label for="username">Create Username:</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Create Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">Register</button>

                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
