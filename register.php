<?php
/**
 * Register for AQUAPAY
 * Redirects to process_register.php for processing
 */
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
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>
