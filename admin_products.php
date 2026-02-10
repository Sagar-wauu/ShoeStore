<?php
include 'dp.php';
include 'auth.php';

require_admin();

$msg = '';
$action = $_GET['action'] ?? '';

// Delete product
if($action === 'delete' && isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    // Only delete the product; do not delete related order_items
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param('i', $product_id);
    if($stmt->execute()) {
        $msg = '✓ Product deleted successfully!';
    } else {
        // Check for  foreign key constraint error
        if ($conn->errno == 1451 || strpos($conn->error, 'foreign key constraint') !== false) {
            $msg = '❌ Cannot  delete product: It is referenced in existing orders.';
        } else {
            $msg = '❌ Error deleting product';
        }
    }
    $stmt->close();
}

// Update stock quantity
if($action === 'update_stock' && isset($_POST['product_id']) && isset($_POST['new_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $new_quantity = intval($_POST['new_quantity']);
    
    if($new_quantity < 0) {
        $msg = '❌ Stock quantity cannot be negative';
    } else {
        $stmt = $conn->prepare("UPDATE products SET quantity=? WHERE id=?");
        $stmt->bind_param('ii', $new_quantity, $product_id);
        if($stmt->execute()) {
            $msg = '✓ Stock updated successfully to ' . $new_quantity . ' units!';
        } else {
            $msg = '❌ Error updating stock';
        }
        $stmt->close();
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Manage Products - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        .admin-container { max-width:1200px; margin:0 auto; padding:20px; }
        .btn { display:inline-block; padding:10px 20px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; border:none; cursor:pointer; margin-bottom:20px; }
        .btn:hover { background:#1565c0; }
        .btn-danger { background:#d32f2f; }
        .btn-danger:hover { background:#c62828; }
        .btn-edit { background:#f57c00; padding:5px 10px; font-size:12px; }
        .btn-edit:hover { background:#e64a19; }
        .message { padding:10px; border-radius:4px; margin-bottom:20px; }
        .message.success { background:#d4edda; color:#155724; }
        .message.error { background:#f8d7da; color:#721c24; }
        .table { width:100%; border-collapse:collapse; background:white; }
        .table th { background:#f5f5f5; padding:12px; text-align:left; border-bottom:2px solid #ddd; }
        .table td { padding:12px; border-bottom:1px solid #ddd; }
        .table tr:hover { background:#f9f9f9; }
        .actions { display:flex; gap:10px; }
        img.thumb { max-width:50px; height:auto; }
        .stock-form { display:flex; gap:5px; align-items:center; }
        .stock-input { width:70px; padding:5px; border:1px solid #ddd; border-radius:4px; }
        .btn-stock { padding:5px 10px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px; }
        .btn-stock:hover { background:#218838; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛡️ Admin Panel</div>
    <ul class="nav-links" id="navLinks">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="admin_products.php">Products</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_users.php">Users</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
</nav>

<div class="admin-container">
    <h1>Manage Products</h1>
    
    <?php if($msg): ?>
        <div class="message <?php echo strpos($msg, '✓') === 0 ? 'success' : 'error'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    
    <a href="admin_add_product.php" class="btn">+ Add New Product</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Price (Rs.)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <?php if($row['image']): ?>
                                <img src="Product images/<?php echo htmlspecialchars($row['image']); ?>" alt="Product" class="thumb">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['brand'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['category'] ?? ''); ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form method="POST" action="admin_products.php?action=update_stock" style="display:flex; gap:5px; align-items:center;">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="new_quantity" value="<?php echo $row['quantity'] ?? 0; ?>" min="0" style="width:60px; padding:5px;">
                                <button type="submit" style="padding:5px 10px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px;">Update</button>
                            </form>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="admin_edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="admin_products.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#999;">No products yet. <a href="admin_add_product.php">Add one now</a></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
