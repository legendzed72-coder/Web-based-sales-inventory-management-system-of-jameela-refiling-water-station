<?php
// store/order entry module
require_once __DIR__ . '/../config.php';

// show order form if buy param set
$buyId = isset($_GET['buy']) ? intval($_GET['buy']) : 0;
$buyQty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
$prod = null;
if ($buyId > 0) {
    // fetch product info
    $stmt = $conn->prepare("SELECT id,name,price,stock FROM products WHERE id=?");
    $stmt->bind_param('i', $buyId);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<div class="content-grid">
<?php if (isset($prod) && $prod): ?>
    <div class="card" style="grid-column:1/-1;text-align:center;">
        <h3>AQUAPAY STORE</h3>
        <form method="post" action="process_order.php" class="store-form">
            <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
            <p><strong><?= htmlspecialchars($prod['name']) ?></strong></p>
            <p>PHP <?= number_format($prod['price'],2) ?></p>
            <p>Stock: <?= $prod['stock'] ?></p>
            <label>Quantity</label><br>
            <input type="number" name="quantity" value="<?= max(1, min($buyQty, $prod['stock'])) ?>" min="1" max="<?= $prod['stock'] ?>" required><br><br>
            <label>Name</label><br>
            <input type="text" name="customer_name" required><br><br>
            <label>Address</label><br>
            <input type="text" name="address" required><br><br>
            <label>Payment method</label><br>
            <input type="text" name="payment_method" required><br><br>
            <div class="store-buttons">
                <button type="submit" name="delivery" value="pickup" class="btn btn-primary">PICK UP</button>
                <button type="submit" name="delivery" value="deliver" class="btn btn-primary">DELIVER</button>
            </div>
            <button type="button" class="btn btn-danger" onclick="window.location.href='?page=store';">CANCEL</button>
        </form>
    </div>
<?php else: ?>
    <?php
    // show product list
    $products = [];
    // only show available products
    $result = $conn->query("SELECT id,name,price,stock,image FROM products WHERE stock > 0 ORDER BY name ASC");
    if ($result) {
        while ($r = $result->fetch_assoc()) {
            $products[] = $r;
        }
    }
    foreach ($products as $p): ?>
        <div class="card product-card">
            <div class="prod-image">
                <?php if ($p['image']): ?>
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                <?php else: ?>
                    <div class="no-image">No Image</div>
                <?php endif; ?>
            </div>
            <p><?= htmlspecialchars($p['name']) ?></p>
            <p>PHP <?= number_format($p['price'],2) ?></p>
            <p>Stock: <?= $p['stock'] ?></p>
            <form method="get" action="">
                <input type="hidden" name="page" value="store">
                <input type="hidden" name="buy" value="<?= $p['id'] ?>">
                <label>Qty</label>
                <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" style="width:60px;">
                <button type="submit" class="btn btn-primary">BUY</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
