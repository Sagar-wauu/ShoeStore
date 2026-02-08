<?php 
include 'dp.php';
include 'auth.php';

$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
if($category) {
    $category = $conn->real_escape_string($category);
    $query .= " AND category='$category'";
}
if($brand) {
    $brand = $conn->real_escape_string($brand);
    $query .= " AND brand='$brand'";
}

// Sorting
if($sort === 'price_low') {
    $query .= " ORDER BY price ASC";
} elseif($sort === 'price_high') {
    $query .= " ORDER BY price DESC";
} else {
    $query .= " ORDER BY created_at DESC";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background:#f5f5f5; }
        .container { max-width:1200px; margin:0 auto; padding:20px; }
        .filters { display:flex; gap:30px; margin-bottom:30px; flex-wrap:wrap; }
        .filter-group label { display:block; margin-bottom:10px; font-weight:bold; }
        .filter-group a, .filter-group select { display:block; margin-bottom:8px; color:#1976d2; text-decoration:none; }
        .filter-group a:hover { text-decoration:underline; }
        .filter-group a.active { color:#d32f2f; font-weight:bold; }
        select { padding:8px; border:1px solid #ddd; border-radius:4px; }
        .products-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .products-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:20px; }
        .product-card { background:white; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); overflow:hidden; transition:transform 0.2s; }
        .product-card:hover { transform:translateY(-5px); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
        .product-card img { width:100%; height:200px; object-fit:cover; }
        .product-card-body { padding:15px; }
        .product-card h3 { margin:0 0 10px 0; font-size:16px; }
        .product-card p { margin:5px 0; font-size:14px; color:#666; }
        .product-card .price { font-size:18px; font-weight:bold; color:#d32f2f; margin:10px 0; }
        .product-card .brand { font-size:12px; color:#999; }
        .btn { display:inline-block; padding:8px 15px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; border:none; cursor:pointer; font-size:14px; }
        .btn:hover { background:#1565c0; }
        .no-products { text-align:center; padding:40px; color:#999; }
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

<div class="container">
    <h1>👟 All Products</h1>
    
    <div class="filters">
        <div class="filter-group">
            <label>Category:</label>
            <a href="products.php" class="<?php echo $category === '' ? 'active' : ''; ?>">All Shoes</a>
            <a href="products.php?category=Male" class="<?php echo $category === 'Male' ? 'active' : ''; ?>">Male Shoes</a>
            <a href="products.php?category=Female" class="<?php echo $category === 'Female' ? 'active' : ''; ?>">Female Shoes</a>
            <a href="products.php?category=Kid" class="<?php echo $category === 'Kid' ? 'active' : ''; ?>">Kid Shoes</a>
        </div>
        
        <div class="filter-group">
            <label>Sort By:</label>
            <select onchange="window.location.href='products.php?category=<?php echo urlencode($category); ?>&sort=' + this.value;">
                <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest</option>
                <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>
        </div>
    </div>
    
    <div class="products-header">
        <h2><?php 
            if($brand) echo 'Brand: ' . htmlspecialchars($brand);
            elseif($category) echo htmlspecialchars($category);
            else echo 'All Products'; 
        ?> (<?php echo $result->num_rows; ?> items)</h2>
    </div>
    
    <?php if($result->num_rows > 0): ?>
        <div class="products-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="Product images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="product-card-body">
                        <p class="brand"><?php echo htmlspecialchars($row['brand'] ?? 'Brand'); ?></p>
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">Rs. <?php echo number_format($row['price'], 2); ?></p>
                        <p><?php echo (isset($row['quantity']) && $row['quantity'] > 0) ? '✓ In Stock' : '✗ Out of Stock'; ?></p>
                        <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-products">
            <p>No products found in this category.</p>
            <a href="products.php" style="color:#1976d2;">View All Products</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
