<?php
// pending orders module
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<div class="content-grid">
    <?php if ($msg): ?>
    <div class="card" style="grid-column:1/-1;background:#d4edda;border:1px solid #c3e6cb;color:#155724;">
        <p><?= htmlspecialchars($msg) ?></p>
    </div>
    <?php endif; ?>
    <div class="card" style="grid-column:1/-1;">
        <h3>Pending Orders</h3>
    </div>
    <?php
    // fetch pending store orders
    $conn = getDB();
    $res = $conn->query("SELECT * FROM store_orders WHERE status='pending' ORDER BY created_at DESC");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            ?>
            <div class="card">
                <p><strong>Name:</strong> <?= htmlspecialchars($row['customer_name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($row['payment_method']) ?></p>
                <p><strong>Delivery:</strong> <?= htmlspecialchars($row['delivery_type']) ?></p>
                <p><strong>Placed:</strong> <?= $row['created_at'] ?></p>
                <form method="post" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="action" value="cancel" class="btn btn-danger">DECLINE</button>
                    <button type="submit" name="action" value="accept" class="btn btn-primary">ACCEPT</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo '<p>No pending orders.</p>';
    }
    ?>
</div>