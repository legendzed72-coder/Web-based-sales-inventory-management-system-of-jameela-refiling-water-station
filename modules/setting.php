<?php
// settings module with sub-options
$subpage = isset($_GET['sub']) ? $_GET['sub'] : 'main';
?>
<div class="content-grid">
    <?php if($subpage === 'main'): ?>
        <div class="card">
            <h3>Settings</h3>
            <div class="side-nav">
                <a href="?page=setting&sub=username">Change Username</a>
                <a href="?page=setting&sub=password">Change Password</a>
                <a href="?page=setting&sub=delete">Delete Account</a>
            </div>
        </div>
    <?php elseif($subpage === 'username'): ?>
        <div class="card">
            <h3>Change Username</h3>
            <form method="post" action="change_username.php">
                <label>Current username:</label><br>
                <input type="text" name="current_username" required><br>
                <label>Create new username:</label><br>
                <input type="text" name="new_username" required><br>
                <button type="submit">Save</button>
            </form>
        </div>
    <?php elseif($subpage === 'password'): ?>
        <div class="card">
            <h3>Change Password</h3>
            <form method="post" action="change_password.php">
                <label>Current password:</label><br>
                <input type="password" name="current_password" required><br>
                <label>Create new password:</label><br>
                <input type="password" name="new_password" required><br>
                <label>Re-enter new password:</label><br>
                <input type="password" name="confirm_password" required><br>
                <button type="submit">Save</button>
            </form>
        </div>
    <?php elseif($subpage === 'delete'): ?>
        <div class="card">
            <h3>Delete Account</h3>
            <form method="post" action="delete_account.php">
                <label>Enter password:</label><br>
                <input type="password" name="password" required><br>
                <label>Re-enter password:</label><br>
                <input type="password" name="confirm_password" required><br>
                <button type="submit">Delete</button>
            </form>
        </div>
    <?php endif; ?>
</div>