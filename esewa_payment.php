<?php
include 'dp.php';
include 'auth.php';

require_login();

$order_id = $_GET['order_id'] ?? null;

if(!$order_id) {
    header('Location: cart.php');
    exit;
}

// Get order
$stmt = $conn->prepare("
    SELECT id, user_id, total_amount 
    FROM orders 
    WHERE id=? AND user_id=?
");
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$order) {
    header('Location: cart.php');
    exit;
}

// eSewa Configuration
// Test merchant credentials (replace with production creds when ready)
$MERCHANT_CODE = 'EPAYTEST';
$MERCHANT_SECRET = '8gBm/:&EnhH.1/q';

// eSewa API Endpoints
$ESEWA_URL_TEST = 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'; // Test
$ESEWA_URL_PROD = 'https://epay.esewa.com.np/api/epay/main/v2/form'; // Production

// Use test endpoint
$ESEWA_URL = $ESEWA_URL_TEST;
$SUCCESS_URL = 'http://localhost/ShoeStore/esewa_success.php';
$FAILURE_URL = 'http://localhost/ShoeStore/esewa_failure.php';

// Generate unique transaction ID (store in session for later verification)
// Check and add transaction_id column if missing (Auto-migration)
$check_col = $conn->query("SHOW COLUMNS FROM orders LIKE 'transaction_id'");
if($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(255) AFTER order_status");
}

// Generate unique transaction ID (User specified format)
// Format: YYYYMMDD-HHMMSS-UserID-UniqueHex
$user_id_safe = $_SESSION['user_id'] ?? 0;
$transaction_id = date("Ymd-His") . "-" . $user_id_safe . "-" . uniqid();
$_SESSION['transaction_id'] = $transaction_id;

$amount = number_format($order['total_amount'], 2, '.', '');
$total_amount = $amount; // keep legacy name used in some places

// Signed fields and signature (eSewa expects base64-encoded HMAC-SHA256)
$signed_field_names = "total_amount,transaction_uuid,product_code";
$signature_data = "total_amount={$total_amount},transaction_uuid={$transaction_id},product_code={$MERCHANT_CODE}";
$signature = base64_encode(hash_hmac('sha256', $signature_data, $MERCHANT_SECRET, true));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-container { max-width:600px; margin:40px auto; padding:20px; }
        .payment-box { background:white; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); text-align:center; }
        .payment-box h1 { color:#1976d2; margin-bottom:10px; }
        .order-info { background:#f5f5f5; padding:20px; border-radius:8px; margin:20px 0; text-align:left; }
        .info-row { display:flex; justify-content:space-between; margin-bottom:10px; }
        .info-row .label { font-weight:bold; }
        .esewa-info { background:#e3f2fd; padding:15px; border-radius:8px; margin:20px 0; text-align:left; }
        .esewa-info h3 { margin-top:0; color:#1976d2; }
        .esewa-info p { margin:5px 0; font-size:14px; }
        .btn { display:inline-block; padding:12px 40px; background:#27ae60; color:white; text-decoration:none; border:none; border-radius:4px; cursor:pointer; font-size:16px; margin:10px 5px; }
        .btn:hover { background:#229954; }
        .btn-secondary { background:#666; }
        .btn-secondary:hover { background:#555; }
        .esewa-logo { height:40px; margin:20px 0; }
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
    </ul>
</nav>

<div class="payment-container">
    <div class="payment-box">
        <h1>💳 Complete Payment</h1>
        <p>Secure payment with eSewa</p>
        
        <div class="order-info">
            <div class="info-row">
                <span class="label">Order ID:</span>
                <span>#<?php echo $order_id; ?></span>
            </div>
            <div class="info-row">
                <span class="label">Amount:</span>
                <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span>eSewa</span>
            </div>
        </div>
        
        <div class="esewa-info">
            <h3>eSewa Payment Gateway</h3>
            <p>✓ Secure and trusted payment gateway</p>
            <p>✓ Instant payment confirmation</p>
            <p>✓ Multiple payment options</p>
            <p style="font-size:12px; color:#666; margin-top:10px;">Your payment information is secured with SSL encryption</p>
        </div>
        
        <form method="POST" action="<?php echo $ESEWA_URL; ?>">
            <!-- eSewa Form Fields -->
            <input type="hidden" id="amount" name="amount" value="<?php echo $amount; ?>">
            <input type="hidden" id="tax_amount" name="tax_amount" value="0">
            <input type="hidden" id="total_amount" name="total_amount" value="<?php echo $amount; ?>">
            <input type="hidden" id="transaction_uuid" name="transaction_uuid" value="<?php echo $transaction_id; ?>">
            <input type="hidden" id="product_code" name="product_code" value="<?php echo $MERCHANT_CODE; ?>">
            <input type="hidden" id="product_service_charge" name="product_service_charge" value="0">
            <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="0">
            <input type="hidden" id="success_url" name="success_url" value="<?php echo $SUCCESS_URL; ?>">
            <input type="hidden" id="failure_url" name="failure_url" value="<?php echo $FAILURE_URL; ?>">
            <input type="hidden" id="signed_field_names" name="signed_field_names" value="<?php echo $signed_field_names; ?>">
            <input type="hidden" id="signature" name="signature" value="<?php echo $signature; ?>">
            
            <button type="submit" class="btn">
                🔒 Pay with eSewa (Rs. <?php echo number_format($order['total_amount'], 2); ?>)
            </button>
        </form>
        
        <a href="checkout.php" class="btn btn-secondary">← Back to Checkout</a>
    </div>
</div>

</body>
</html>
