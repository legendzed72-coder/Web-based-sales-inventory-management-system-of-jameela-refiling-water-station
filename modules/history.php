<?php
// history module placeholder
?>
<div class="content-grid">
    <div class="card" style="grid-column:1/-1;">
        <h3>Finished Orders</h3>
    </div>
    <?php
    $conn = getDB();
    $res = $conn->query("SELECT * FROM store_orders WHERE status='completed' ORDER BY created_at DESC");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            ?>
            <div class="card">
                <p><strong>Name:</strong> <?= htmlspecialchars($row['customer_name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($row['payment_method']) ?></p>
                <p><strong>Delivery:</strong> <?= htmlspecialchars($row['delivery_type']) ?></p>
                <p><strong>Placed:</strong> <?= $row['created_at'] ?></p>
            </div>
            <?php
        }
    } else {
        echo '<p>No completed orders.</p>';
    }
    ?>
    <div class="card" style="grid-column:1/-1;margin-top:20px;">
        <h3>Canceled Orders</h3>
    </div>
    <?php
    $cres = $conn->query("SELECT * FROM store_orders WHERE status='cancelled' ORDER BY created_at DESC");
    if ($cres && $cres->num_rows > 0) {
        while ($row = $cres->fetch_assoc()) {
            ?>
            <div class="card">
                <p><strong>Name:</strong> <?= htmlspecialchars($row['customer_name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                <p><strong>Payment Method:</strong> <?= htmlspecialchars($row['payment_method']) ?></p>
                <p><strong>Delivery:</strong> <?= htmlspecialchars($row['delivery_type']) ?></p>
                <p><strong>Placed:</strong> <?= $row['created_at'] ?></p>
            </div>
            <?php
        }
    } else {
        echo '<p>No cancelled orders.</p>';
    }
    ?>
</div>