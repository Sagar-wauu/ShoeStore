<?php
include 'dp.php';
include 'auth.php';

$product_id = $_GET['id'] ?? null;

if(!$product_id) {
    header('Location: products.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if(!$product) {
    header('Location: products.php');
    exit;
}

// Get related products
$related = $conn->query("SELECT id, name, price, image FROM products WHERE category='{$product['category']}' AND id != {$product_id} LIMIT 4");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($product['name']); ?> - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .product-detail-container { max-width:1000px; margin:20px auto; padding:20px; }
        .product-detail { display:grid; grid-template-columns:400px 1fr; gap:40px; margin-bottom:40px; }
        @media (max-width:768px) {
            .product-detail { grid-template-columns:1fr; gap:20px; }
        }
        .product-image { background:#f5f5f5; border-radius:8px; overflow:hidden; }
        .product-image img { width:100%; height:auto; display:block; }
        .product-info h1 { margin:0 0 20px 0; font-size:28px; }
        .price { font-size:24px; color:#d32f2f; font-weight:bold; margin-bottom:20px; }
        .rating { margin-bottom:20px; }
        .specs { background:#f9f9f9; padding:15px; border-radius:8px; margin-bottom:20px; }
        .specs-row { display:flex; margin-bottom:10px; }
        .specs-row span { flex:0 0 100px; font-weight:bold; }
        .specs-row span:last-child { flex:1; }
        .description { margin-bottom:20px; line-height:1.6; color:#555; }
        .qty-selector { display:flex; align-items:center; gap:10px; margin-bottom:20px; }
        .qty-selector input { width:60px; padding:8px; border:1px solid #ddd; border-radius:4px; }
        .btn-primary { background:#1976d2; color:white; padding:12px 30px; border:none; border-radius:4px; cursor:pointer; font-size:16px; }
        .btn-primary:hover { background:#1565c0; }
        .btn-primary.disabled { background:#ccc; cursor:not-allowed; }
        .related { margin-top:40px; }
        .related-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:20px; }
        .card { background:white; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); overflow:hidden; }
        .card img { width:100%; height:200px; object-fit:cover; }
        .card-body { padding:15px; }
        .card-body h3 { margin:0 0 10px 0; }
        .card-body .price { margin:10px 0; }
        .card-body a { display:inline-block; color:#1976d2; text-decoration:none; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛒 ShoeStore</div>
    <ul class="nav-links">
        <li><a href="front.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <?php if(is_logged_in()): ?>
            <li><a href="profile.php">👤 Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="product-detail-container">
    <a href="products.php" style="color:#1976d2; text-decoration:none;">← Back to Products</a>
    
    <div class="product-detail">
        <div class="product-image">
            <img src="Product images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="price">Rs. <?php echo number_format($product['price'], 2); ?></div>
            
            <div class="rating">⭐⭐⭐⭐⭐ (12 reviews)</div>
            
            <div class="specs">
                <div class="specs-row">
                    <span>Brand:</span>
                    <span><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></span>
                </div>
                <div class="specs-row">
                    <span>Category:</span>
                    <span><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></span>
                </div>
                <div class="specs-row">
                    <span>Color:</span>
                    <span><?php echo htmlspecialchars($product['color'] ?? 'N/A'); ?></span>
                </div>
                <div class="specs-row">
                    <span>Size:</span>
                    <span><?php echo htmlspecialchars($product['size'] ?? 'N/A'); ?></span>
                </div>
                <div class="specs-row">
                    <span>In Stock:</span>
                    <span><?php echo (isset($product['quantity']) && $product['quantity'] > 0) ? '✓ Yes' : '✗ Out of Stock'; ?></span>
                </div>
            </div>
            
            <div class="description">
                <strong>Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($product['description'] ?? 'N/A')); ?>
            </div>
            
            <form method="POST" action="cart.php">
                <div class="qty-selector">
                    <label>Quantity:</label>
                    <input type="number" name="qty" value="1" min="1" max="<?php echo max(1, $product['quantity'] ?? 0); ?>" required>
                </div>
                
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <?php if(isset($product['quantity']) && $product['quantity'] > 0): ?>
                    <button type="submit" class="btn-primary">Add to Cart 🛒</button>
                <?php else: ?>
                    <button type="button" class="btn-primary disabled">Out of Stock</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if($related->num_rows > 0): ?>
        <div class="related">
            <h2>Related Products</h2>
            <div class="related-grid">
                <?php while($rel = $related->fetch_assoc()): ?>
                    <div class="card">
                        <img src="Product images/<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($rel['name']); ?></h3>
                            <div class="price">Rs. <?php echo number_format($rel['price'], 2); ?></div>
                            <a href="product_details.php?id=<?php echo $rel['id']; ?>">View Details →</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
