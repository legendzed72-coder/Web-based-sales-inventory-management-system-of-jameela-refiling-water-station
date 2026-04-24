<?php
/**
 * Dashboard Header - Functional header with user info and logout
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Get user info if logged in
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $username;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>
<!-- Dashboard Header -->
<div class="dashboard-header" style="display:flex;align-items:center;justify-content:space-between;padding:15px 20px;background:#66ccff;box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex;align-items:center;gap:20px;">
        <div style="font-size:24px;color:#003366;font-weight:bold;">AQUAPAY</div>
        <nav style="display:flex;gap:15px;">
            <a href="dashboard.php" style="color:#003366;text-decoration:none;font-weight:500;">Dashboard</a>
            <a href="userdashboard.php" style="color:#003366;text-decoration:none;font-weight:500;">Store</a>
        </nav>
    </div>
    <div style="display:flex;align-items:center;gap:15px;">
        <?php if ($user_id > 0): ?>
            <span style="color:#003366;">Welcome, <strong><?= htmlspecialchars($full_name) ?></strong></span>
            <a href="logout.php" style="background:#dc3545;color:white;padding:8px 15px;border-radius:4px;text-decoration:none;font-size:14px;">Logout</a>
        <?php else: ?>
            <a href="login.php" style="color:#003366;text-decoration:none;">Login</a>
        <?php endif; ?>
    </div>
</div>
