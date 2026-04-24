<?php
// report module content
require_once __DIR__ . '/../config.php';
// gather some simple statistics
$total_users = 0;
$total_orders = 0;
$total_products = 0;

$res = $conn->query("SELECT COUNT(*) as c FROM users");
if ($res) { $total_users = $res->fetch_assoc()['c']; }
$res = $conn->query("SELECT COUNT(*) as c FROM store_orders");
if ($res) { $total_orders = $res->fetch_assoc()['c']; }
$res = $conn->query("SELECT COUNT(*) as c FROM products");
if ($res) { $total_products = $res->fetch_assoc()['c']; }
?>
<div class="content-grid">
    <div class="card" style="grid-column:1/-1;">
        <h3>Summary</h3>
        <p>Total users: <?= $total_users ?></p>
        <p>Total orders: <?= $total_orders ?></p>
        <p>Total products: <?= $total_products ?></p>
    </div>
    <?php
    // fetch recent orders
    $recent_orders = [];
    $rres = $conn->query("SELECT so.id, so.status, so.created_at, u.username
                         FROM store_orders so
                         JOIN users u ON so.user_id = u.id
                         ORDER BY so.created_at DESC
                         LIMIT 5");
    if ($rres) {
        while ($orow = $rres->fetch_assoc()) {
            $recent_orders[] = $orow;
        }
    }
    ?>
    <?php if (!empty($recent_orders)): ?>
    <div class="card" style="grid-column:1/-1;">
        <h3>Recent Orders</h3>
        <table class="daily-table">
            <thead><tr><th>ID</th><th>User</th><th>Status</th><th>Placed</th></tr></thead>
            <tbody>
            <?php foreach($recent_orders as $ro): ?>
            <tr>
                <td><?= $ro['id'] ?></td>
                <td><?= htmlspecialchars($ro['username']) ?></td>
                <td><?= htmlspecialchars($ro['status']) ?></td>
                <td><?= $ro['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    <!-- weekly sales chart -->
    <div class="card" id="weekly-sales">
        <h3>Weekly Sales</h3>
        <canvas id="salesChart" width="400" height="200"></canvas>
    </div>
    <!-- weekly report boxes -->
    <div class="card" id="weekly-report">
        <h3>Weekly Report</h3>
        <?php
            $weekLabels = ['1st Week','2nd Week','3rd Week','4th Week'];
            foreach ($weekLabels as $label): ?>
            <div class="weekly-report-box">
                <h4><?= $label ?></h4>
                <p>Total sale:</p>
                <p>Gross:</p>
                <p>Net:</p>
                <p>Expense:</p>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- daily table -->
    <div class="card" id="daily-report">
        <h3>Daily</h3>
        <table class="daily-table">
            <thead>
                <tr>
                    <th>DAY</th>
                    <th>GROSS INCOME</th>
                    <th>NET INCOME</th>
                    <th>EXPENSES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
                foreach($days as $d): ?>
                <tr>
                    <td><?= $d ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Total</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- annual report grid -->
    <div class="card" id="annual-report">
        <h3>Annual Report</h3>
        <div class="content-grid">
            <?php
            $months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
            foreach ($months as $m): ?>
            <div class="weekly-report-box">
                <h4><?= $m ?></h4>
                <p>Total sale:</p>
                <p>Gross:</p>
                <p>Net:</p>
                <p>Expense:</p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>