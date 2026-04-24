<?php
// product management module for admins
require_once __DIR__ . '/../config.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Ensure uploads directory exists
$uploads_dir = __DIR__ . '/../uploads/products';
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}
?>

<div class="content-grid">
    <?php if ($msg): ?>
    <div class="card" style="grid-column:1/-1;background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:12px;border-radius:4px;">
        <?= htmlspecialchars($msg) ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="card" style="grid-column:1/-1;background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:12px;border-radius:4px;">
        Error: <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <!-- List all products -->
        <div class="card" style="grid-column:1/-1;display:flex;justify-content:space-between;align-items:center;">
            <h3>Product Management</h3>
            <a href="?page=products&action=add" class="btn btn-primary">+ Add New Product</a>
        </div>
        
        <?php
        $result = $conn->query("SELECT * FROM products ORDER BY name ASC");
        if ($result && $result->num_rows > 0):
            while ($product = $result->fetch_assoc()):
        ?>
        <div class="card" style="display:grid;grid-template-columns:120px 1fr auto;gap:20px;align-items:center;">
            <div style="text-align:center;">
                <?php if ($product['image'] && file_exists(__DIR__ . '/../' . $product['image'])): ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" style="max-width:100px;max-height:100px;border-radius:4px;">
                <?php else: ?>
                    <div style="width:100px;height:100px;background:#ccc;display:flex;align-items:center;justify-content:center;border-radius:4px;color:#666;">No Image</div>
                <?php endif; ?>
            </div>
            
            <div>
                <h4 style="margin:0 0 5px 0;"><?= htmlspecialchars($product['name']) ?></h4>
                <p style="margin:3px 0;"><strong>Price:</strong> PHP <?= number_format($product['price'], 2) ?></p>
                <p style="margin:3px 0;"><strong>Stock:</strong> <?= $product['stock'] ?></p>
                <p style="margin:3px 0;"><strong>Category:</strong> <?= htmlspecialchars($product['category'] ?? 'N/A') ?></p>
                <p style="margin:3px 0;font-size:12px;color:#666;"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 100)) ?>...</p>
            </div>
            
            <div style="display:flex;gap:8px;flex-direction:column;">
                <a href="?page=products&action=edit&id=<?= $product['id'] ?>" class="btn btn-primary" style="font-size:12px;padding:6px 12px;">Edit</a>
                <a href="javascript:if(confirm('Delete this product?')) window.location.href='delete_product.php?id=<?= $product['id'] ?>';" class="btn btn-danger" style="font-size:12px;padding:6px 12px;">Delete</a>
            </div>
        </div>
        <?php
            endwhile;
        else:
        ?>
        <div class="card" style="grid-column:1/-1;text-align:center;color:#666;">
            No products found.
        </div>
        <?php endif; ?>

    <?php elseif ($action === 'add'): ?>
        <!-- Add new product form -->
        <div class="card" style="grid-column:1/-1;">
            <h3>Add New Product</h3>
            <form method="post" action="save_product.php" enctype="multipart/form-data" style="max-width:600px;">
                <input type="hidden" name="product_id" value="0">
                
                <label><strong>Product Name</strong></label>
                <input type="text" name="name" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                
                <label><strong>Description</strong></label>
                <textarea name="description" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;height:100px;"></textarea>
                
                <label><strong>Price (PHP)</strong></label>
                <input type="number" name="price" step="0.01" min="0" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                
                <label><strong>Stock Quantity</strong></label>
                <input type="number" name="stock" min="0" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                
                <label><strong>Category</strong></label>
                <input type="text" name="category" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                
                <label><strong>Product Image</strong></label>
                <input type="file" name="image" accept="image/*" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <a href="?page=products" class="btn" style="background:#6c757d;">Cancel</a>
                </div>
            </form>
        </div>

    <?php elseif ($action === 'edit' && $product_id > 0): ?>
        <!-- Edit product form -->
        <?php
        $result = $conn->query("SELECT * FROM products WHERE id=$product_id");
        $product = $result ? $result->fetch_assoc() : null;
        if (!$product):
        ?>
        <div class="card" style="grid-column:1/-1;color:#721c24;">
            Product not found.
        </div>
        <?php else: ?>
        <div class="card" style="grid-column:1/-1;">
            <h3>Edit Product</h3>
            <form method="post" action="save_product.php" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <label><strong>Product Name</strong></label>
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                        
                        <label><strong>Description</strong></label>
                        <textarea name="description" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;height:100px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        
                        <label><strong>Price (PHP)</strong></label>
                        <input type="number" name="price" step="0.01" min="0" value="<?= $product['price'] ?>" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                        
                        <label><strong>Stock Quantity</strong></label>
                        <input type="number" name="stock" min="0" value="<?= $product['stock'] ?>" required style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                        
                        <label><strong>Category</strong></label>
                        <input type="text" name="category" value="<?= htmlspecialchars($product['category'] ?? '') ?>" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                    </div>
                    
                    <div>
                        <label><strong>Current Image</strong></label>
                        <div style="margin-bottom:15px;">
                            <?php if ($product['image'] && file_exists(__DIR__ . '/../' . $product['image'])): ?>
                                <img src="<?= htmlspecialchars($product['image']) ?>" style="max-width:100%;max-height:200px;border-radius:4px;margin-bottom:10px;">
                                <p style="font-size:12px;color:#666;margin:5px 0;">Current: <?= basename($product['image']) ?></p>
                            <?php else: ?>
                                <div style="width:100%;height:150px;background:#ccc;display:flex;align-items:center;justify-content:center;border-radius:4px;color:#666;margin-bottom:10px;">No Image</div>
                            <?php endif; ?>
                        </div>
                        
                        <label><strong>Upload New Image (optional)</strong></label>
                        <input type="file" name="image" accept="image/*" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                        <p style="font-size:12px;color:#666;">Leave empty to keep current image</p>
                    </div>
                </div>
                
                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a href="?page=products" class="btn" style="background:#6c757d;">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<style>
    .card a.btn {
        display: inline-block;
    }
</style>
