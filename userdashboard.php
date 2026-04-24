<?php
/**
 * User Dashboard / Store for AQUAPAY
 */
require_once 'auth_check.php';
require_once 'config.php';

// Get current user info
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'] ?? $username;

// handle optional buy request
$buyId = isset($_GET['buy']) ? intval($_GET['buy']) : 0;
$buyQty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
$prod = null;
if ($buyId > 0) {
    $stmt = $conn->prepare("SELECT id,name,price,stock FROM products WHERE id=?");
    $stmt->bind_param('i', $buyId);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get products from database
$products = [];
$result = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AQUAPAY STORE</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#e0f7ff;margin:0;padding:0;}
        .container{display:flex;max-width:1200px;margin:20px auto;background:#fff;border-radius:8px;overflow:hidden;}
        .store{flex:3;padding:20px;}
        .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
        .header h1{color:#003366;letter-spacing:1px;}
        .header .search{position:relative;}
        .header input[type=search]{padding:6px 30px 6px 10px;border:1px solid #ccc;border-radius:4px;}
        .header .cart{font-size:24px;color:#003366;text-decoration:none;}
        .products{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:15px;}
        .card{background:#f2f2f2;border-radius:6px;padding:15px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,.1);}
        .card img{max-width:100px;height:auto;margin:10px 0;}
        .card .name{font-size:14px;font-weight:bold;margin:5px 0;color:#003366;}
        .card .price{font-size:16px;color:#0077b6;font-weight:bold;margin:5px 0;}
        .card .stock{font-size:12px;color:#666;margin:5px 0;}
        .card input[type=number]{width:50px;padding:5px;text-align:center;}
        .card button{background:linear-gradient(90deg,#0073e6,#00c2ff);color:#fff;border:none;padding:8px 15px;border-radius:4px;cursor:pointer;margin-top:5px;}
        .card button:hover{background:linear-gradient(90deg,#005bb5,#0099cc);}
        .side{flex:1;background:linear-gradient(180deg,#a0d8ff,#c4c4ff);padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;}
        .carousel-image{width:100%;height:180px;background:#b0e0ff;margin-bottom:10px;display:flex;align-items:center;justify-content:center;color:#003366;font-size:18px;border-radius:8px;}
        .carousel-info{font-size:14px;color:#003366;text-align:center;margin:5px 0;}
        .no-products{grid-column:1/-1;text-align:center;padding:40px;color:#666;}
        /* order form styling */
        .store-form label{font-weight:bold;color:#003366;text-transform:uppercase;}
        .store-form input{width:80%;padding:6px 8px;border:1px solid #ccc;border-radius:4px;}
        .store-buttons{margin:10px 0;display:flex;gap:10px;justify-content:center;}
        /* generic button classes */
        .btn{padding:8px 16px;border:none;border-radius:4px;cursor:pointer;text-decoration:none;color:#fff;}
        .btn-primary{background:#0077b6;}
        .btn-danger{background:#dc3545;}
    </style>
</head>
<body>

<!-- Header -->
<div class="topbar" style="display:flex;align-items:center;justify-content:space-between;padding:15px 20px;background:#66ccff;">
    <div style="font-size:24px;color:#003366;font-weight:bold;">AQUAPAY</div>
    <div style="display:flex;gap:20px;align-items:center;">
        <span style="color:#003366;">Welcome, <strong><?= htmlspecialchars($full_name) ?></strong></span>
        <a href="#" title="Messages" style="font-size:20px;color:#003366;text-decoration:none;">✉️</a>
        <a href="#" title="Account" style="font-size:20px;color:#003366;text-decoration:none;">👤</a>
        <a href="logout.php" style="color:#003366;text-decoration:none;font-weight:bold;">Logout</a>
    </div>
</div>

<div class="container">
    <div class="store">
        <?php
        $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
        $error = isset($_GET['error']) ? $_GET['error'] : '';
        ?>
        <?php if ($msg): ?>
        <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px;border-radius:4px;margin-bottom:15px;">
            <?= htmlspecialchars($msg) ?>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px;border-radius:4px;margin-bottom:15px;">
            Error: <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <div class="header">
            <h1>AQUAPAY STORE</h1>
            <form class="search">
                <input type="search" placeholder="Search products..." id="searchInput" onkeyup="filterProducts()">
            </form>
            <a class="cart" href="#" title="Shopping Cart">🛒</a>
        </div>
        
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
                    <button type="button" class="btn btn-danger" onclick="window.location.href='userdashboard.php';">CANCEL</button>
                </form>
            </div>
        <?php else: ?>
        <div class="products" id="productsGrid">
            <?php if (empty($products)): ?>
                <div class="no-products">No products available at the moment.</div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <div class="card" data-name="<?= strtolower(htmlspecialchars($product['name'])) ?>">
                    <img src="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23666%22>No Image</text></svg>' ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                         onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22><rect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%23666%22>No Image</text></svg>'">
                    <div class="name"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="price">PHP <?= number_format($product['price'], 2) ?></div>
                    <div class="stock">Stock: <?= $product['stock'] ?></div>
                    <div style="margin-top:10px;">
                        <input type="number" min="1" max="<?= $product['stock'] ?>" value="1">
                    </div>
                    <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>, this.parentElement.querySelector('input[type=number]').value)">BUY</button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="side">
        <div class="carousel-image">Promo Image</div>
        <div class="carousel-info"><strong>Special Offer!</strong></div>
        <div class="carousel-info">Get 10% off on orders above PHP 500!</div>
        <div class="carousel-info">Free delivery within the city</div>
    </div>
</div>

<script>
function filterProducts() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toLowerCase();
    var cards = document.getElementsByClassName('card');
    
    for (var i = 0; i < cards.length; i++) {
        var name = cards[i].getAttribute('data-name');
        if (name.indexOf(filter) > -1) {
            cards[i].style.display = "";
        } else {
            cards[i].style.display = "none";
        }
    }
}

function addToCart(productId, quantity) {
    // redirect to the same page to display order form
    window.location.href = '?buy=' + productId + '&qty=' + quantity;
}
</script>

</body>
</html>
