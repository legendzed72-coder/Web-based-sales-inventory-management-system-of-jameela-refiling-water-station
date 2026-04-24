<?php
/**
 * Login for AQUAPAY
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'config.php';

$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data and sanitize
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful - set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admindashboard.php");
                } else {
                    header("Location: userdashboard.php");
                }
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found. Please register first.";
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
    <title>Login - AQUAPAY</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="login-body">

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
        
        <h2>Login</h2>
        <form id="login-box" action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>

            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
