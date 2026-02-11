<?php
include 'dp.php';
include 'auth.php';

// Allow admin to view any order, user can only view their own
require_login();
$order_id = $_GET['id'] ?? null;
if(!$order_id) {
    header('Location: profile.php');
    exit;
}
if(is_admin()) {
    // Admin: fetch any order
    $order_stmt = $conn->prepare("SELECT id, total_amount, payment_method, payment_status, order_status, shipping_address, phone, city, postal_code, created_at FROM orders WHERE id=?");
    $order_stmt->bind_param('i', $order_id);
} else {
    // User: fetch only their own order
    $user_id = $_SESSION['user_id'];
    $order_stmt = $conn->prepare("SELECT id, total_amount, payment_method, payment_status, order_status, shipping_address, phone, city, postal_code, created_at FROM orders WHERE id=? AND user_id=?");
    $order_stmt->bind_param('ii', $order_id, $user_id);
}
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();
if(!$order) {
    header('Location: profile.php');
    exit;
}

// Fetch order items
$items_stmt = $conn->prepare("
    SELECT product_name, price, quantity, total 
    FROM order_items 
    WHERE order_id=?
");
$items_stmt->bind_param('i', $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
$items_stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #<?php echo $order_id; ?> - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container { max-width:800px; margin:20px auto; padding:20px; }
        .order-card { background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
        .order-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; padding-bottom:20px; border-bottom:2px solid #eee; }
        .status-badge {
            display:inline-block;
            padding:8px 12px;
            border-radius:4px;
            font-weight:bold;
            font-size:14px;
        }
        .status-pending { background:#fff3cd; color:#856404; }
        .status-completed { background:#d4edda; color:#155724; }
        .status-cancelled { background:#f8d7da; color:#721c24; }
        .payment-status { margin-top:10px; font-size:12px; }
        .items-table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        .items-table th, .items-table td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
        .items-table th { background:#f5f5f5; font-weight:bold; }
        .summary { background:#f5f5f5; padding:15px; border-radius:4px; margin-bottom:20px; }
        .summary-row { display:flex; justify-content:space-between; margin-bottom:10px; }
        .summary-row.total { font-size:18px; font-weight:bold; border-top:2px solid #ddd; padding-top:10px; margin-top:10px; }
        .shipping-info { background:#f5f5f5; padding:15px; border-radius:4px; }
        .shipping-info h3 { margin-top:0; }
        .back-link { margin-bottom:20px; }
        .back-link a { color:#1976d2; text-decoration:none; }
        .back-link a:hover { text-decoration:underline; }
    </style>
</head>
<body>

<?php if(!is_admin()): ?>
<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛒 ShoeStore</div>
    <ul class="nav-links">
        <li><a href="front.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<?php endif; ?>

<div class="container">
    <?php if(is_admin()): ?>
    <div class="back-link"><a href="admin_orders.php">← Back to Orders</a></div>
    <?php elseif(!is_admin()): ?>
    <div class="back-link"><a href="profile.php">← Back to My Orders</a></div>
    <?php endif; ?>
    
    <div class="order-card">
        <div class="order-header">
            <div>
                <h1>Order #<?php echo $order_id; ?></h1>
                <p style="color:#666; margin:5px 0;">Placed on <?php echo date('F d, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
            </div>
            <div style="text-align:right;">
                <div class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                    <?php echo strtoupper($order['order_status']); ?>
                </div>
                <div class="payment-status">
                    Payment: <strong><?php echo ucfirst($order['payment_status']); ?></strong>
                </div>
            </div>
        </div>
        
        <h3>Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Quantity</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="text-align:right;">Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align:right;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align:right;">Rs. <?php echo number_format($item['total'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
    </div>
    
    <div class="order-card">
        <div class="shipping-info">
            <h3>Shipping Address</h3>
            <p>
                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                <?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['postal_code']); ?><br>
                Phone: <?php echo htmlspecialchars($order['phone']); ?>
            </p>
        </div>
    </div>
</div>

</body>
</html>
