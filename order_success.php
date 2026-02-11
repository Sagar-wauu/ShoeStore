<?php
include 'dp.php';
include 'auth.php';

require_login();

$order_id = $_GET['order_id'] ?? null;

if(!$order_id) {
    header('Location: front.php');
    exit;
}

// Get order details
$stmt = $conn->prepare("
    SELECT id, user_id, total_amount, payment_method, order_status, created_at 
    FROM orders 
    WHERE id=? AND user_id=?
");
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$order) {
    header('Location: front.php');
    exit;
}

// Get order items
$stmt = $conn->prepare("
    SELECT product_name, quantity, price, total 
    FROM order_items 
    WHERE order_id=?
");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$stmt->close();

// Clear cart
unset($_SESSION['cart']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-container { max-width:700px; margin:40px auto; padding:20px; }
        .success-box { background:white; padding:40px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); text-align:center; }
        .success-icon { font-size:60px; margin-bottom:20px; }
        .success-box h1 { color:#28a745; margin:20px 0; }
        .success-box p { color:#666; font-size:16px; margin:15px 0; }
        .order-details { background:#f5f5f5; padding:20px; border-radius:8px; margin:20px 0; text-align:left; }
        .detail-row { display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #ddd; }
        .detail-row .label { font-weight:bold; }
        .detail-row .value { text-align:right; }
        .order-items { background:#f9f9f9; padding:15px; border-radius:8px; margin:20px 0; }
        .item { display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #eee; }
        .item:last-child { border-bottom:none; }
        .btn { display:inline-block; padding:12px 30px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; margin:10px 5px; }
        .btn:hover { background:#1565c0; }
        .btn-secondary { background:#666; }
        .btn-secondary:hover { background:#555; }
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
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="success-container">
    <div class="success-box">
        <div class="success-icon">✅</div>
        <h1>Order Confirmed!</h1>
        <p>Thank you for your order. Your order has been successfully placed.</p>
        
        <div class="order-details">
            <div class="detail-row">
                <span class="label">Order ID:</span>
                <span class="value">#<?php echo $order['id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Method:</span>
                <span class="value">💵 Cash on Delivery</span>
            </div>
            <div class="detail-row">
                <span class="label">Order Status:</span>
                <span class="value"><strong style="color:#ff9800;">Pending Confirmation</strong></span>
            </div>
            <div class="detail-row">
                <span class="label">Total Amount:</span>
                <span class="value"><strong style="color:#28a745; font-size:18px;">Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></span>
            </div>
            <div class="detail-row">
                <span class="label">Order Date:</span>
                <span class="value"><?php echo date('F d, Y - g:i A', strtotime($order['created_at'])); ?></span>
            </div>
        </div>
        
        <div class="order-items">
            <h3 style="margin-top:0; text-align:left;">Order Items</h3>
            <?php while($item = $items_result->fetch_assoc()): ?>
                <div class="item">
                    <div>
                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                        <small>Qty: <?php echo $item['quantity']; ?> × Rs. <?php echo number_format($item['price'], 2); ?></small>
                    </div>
                    <div style="text-align:right; font-weight:bold;">
                        Rs. <?php echo number_format($item['total'], 2); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div style="background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0; text-align:left;">
            <h4 style="margin-top:0; color:#1976d2;">📦 What's Next?</h4>
            <ul style="margin:0; padding-left:20px;">
                <li>Our team will verify your order within 24 hours</li>
                <li>You'll receive a confirmation email with tracking details</li>
                <li>Payment will be collected when your order is delivered</li>
                <li>You can track your order from your profile</li>
            </ul>
        </div>
        
        <div style="margin-top:30px;">
            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn">View Order Details</a>
            <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

</body>
</html>
