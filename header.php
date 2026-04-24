<?php
/**
 * Header for Dashboard pages
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>
<header id="header">
    <div id="navbar">
      <h1 id="logo">AQUAPAY</h1>
      <nav id="nav">
        <ul>
          <li><a href="dashboard.php">HOME</a></li>
          <li><a href="#">SETTINGS</a></li>
          <?php if ($user_id > 0): ?>
          <li><a href="logout.php" style="color:#dc3545;">LOGOUT</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
</header>
