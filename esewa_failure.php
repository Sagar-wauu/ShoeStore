<?php
include 'dp.php';
include 'auth.php';

require_login();

// Read incoming params from eSewa (if any)
$transaction_uuid = $_GET['transaction_uuid'] ?? ($_GET['transaction_id'] ?? '');
$total_amount = $_GET['total_amount'] ?? ($_GET['amount'] ?? '');

// Mark the latest pending order as failed for this user
$stmt = $conn->prepare("SELECT id FROM orders WHERE user_id=? AND payment_status='pending' ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if($order) {
    $update = $conn->prepare("UPDATE orders SET payment_status='failed', order_status='cancelled' WHERE id=?");
    $update->bind_param('i', $order['id']);
    $update->execute();
    $update->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Failed - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .failure-container { max-width:600px; margin:40px auto; padding:20px; }
        .failure-box { background:white; padding:40px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); text-align:center; }
        .failure-icon { font-size:60px; margin-bottom:20px; }
        .failure-box h1 { color:#d32f2f; margin:20px 0; }
        .failure-box p { color:#666; font-size:16px; line-height:1.6; }
        .error-message { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:15px; border-radius:4px; margin:20px 0; }
        .btn { display:inline-block; padding:12px 30px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; margin:10px 5px; }
        .btn:hover { background:#1565c0; }
        .btn-danger { background:#d32f2f; }
        .btn-danger:hover { background:#c62828; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛒 ShoeStore</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="cart.php">Cart 🛒</a></li>
        <li><a href="login.php">Login</a></li>
    </ul>
</nav>

<div class="failure-container">
    <div class="failure-box">
        <div class="failure-icon">❌</div>
        <h1>Payment Failed</h1>
        
        <div class="error-message">
            <strong>Payment could not be processed</strong><br>
            Please try again or contact support.
        </div>
        
        <p>Your order has not been placed. The payment transaction was cancelled or declined.</p>
        
        <p><strong>Possible Reasons:</strong></p>
        <ul style="text-align:left; display:inline-block;">
            <li>Insufficient balance in your eSewa account</li>
            <li>Transaction was cancelled</li>
            <li>Payment gateway temporarily unavailable</li>
            <li>Incorrect payment credentials</li>
        </ul>
        
        <div style="margin-top:30px;">
            <a href="checkout.php" class="btn">← Try Again</a>
            <a href="cart.php" class="btn btn-danger">Back to Cart</a>
            <a href="index.php" class="btn">Home</a>
        </div>
        
        <p style="margin-top:20px; color:#999; font-size:14px;">
            If you need help, please contact us at support@shoestore.com
        </p>
    </div>
</div>

</body>
</html>
