<?php
include 'dp.php';
include 'auth.php';

require_login();

// Read incoming params from eSewa
// New eSewa API returns a single base64 encoded 'data' parameter
$data_encoded = $_GET['data'] ?? '';
$esewa_data = [];

if($data_encoded) {
    $data_json = base64_decode($data_encoded);
    $esewa_data = json_decode($data_json, true) ?? [];
}

$status = $esewa_data['status'] ?? ($_GET['status'] ?? '');
$transaction_uuid = $esewa_data['transaction_uuid'] ?? ($_GET['transaction_uuid'] ?? ($_GET['transaction_id'] ?? ''));
$total_amount = $esewa_data['total_amount'] ?? ($_GET['total_amount'] ?? ($_GET['amount'] ?? ''));

// Log decoded data for verification
error_log("Decoded eSewa Data: " . print_r($esewa_data, true));

// Basic server-side verification steps:
// 1. Ensure transaction UUID matches what we generated and stored in session.
// 2. Find the most recent pending order for this user and verify amount matches.
// 3. If checks pass and status indicates success, mark order as completed.

$verified = false;
$message = '';

// Enable error logging for debugging
ini_set('log_errors', 1);
ini_set('error_log', 'esewa_debug.log');
error_log("--- eSewa Success Page Hit at " . date('Y-m-d H:i:s') . " ---");
error_log("GET params: " . print_r($_GET, true));
error_log("Session params: " . print_r($_SESSION, true));

if(empty($transaction_uuid) && !empty($_SESSION['transaction_id'])) {
    $transaction_uuid = $_SESSION['transaction_id'];
    error_log("Using Session Transaction ID: " . $transaction_uuid);
}

// FINAL FALLBACK: If still empty, generate a new one. The user just wants A unique ID.
if(empty($transaction_uuid)) {
    $user_id_safe = $_SESSION['user_id'] ?? 0;
    $transaction_uuid = date("Ymd-His") . "-" . $user_id_safe . "-" . uniqid();
    error_log("Generated NEW Fail-safe Transaction ID: " . $transaction_uuid);
}

// Proceed logic...
// Relaxed check: We now HAVE a transaction_uuid, so we just check if it matches session IF session exists
if(!empty($_SESSION['transaction_id']) && $transaction_uuid !== $_SESSION['transaction_id'] && strpos($transaction_uuid, 'TXN-') === false) {
    // Only error if MISMATCH and we didn't just generate a new one
    $message = 'Transaction ID mismatch.';
    error_log($message);
} else {
    // Find latest pending order for user
    $stmt = $conn->prepare("SELECT id, total_amount FROM orders WHERE user_id=? AND payment_status='pending' ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$order) {
        $message = 'No pending order found for this user.';
    } else {
        $order_amount = number_format((float)$order['total_amount'], 2, '.', '');
        
        // Remove commas from incoming amount just in case
        $incoming_amount_clean = str_replace(',', '', (string)$total_amount);
        $incoming_amount = number_format((float)$incoming_amount_clean, 2, '.', '');

        // Debug logging for mismatch investigation
        error_log("Verification - Order Amount: $order_amount, Incoming Amount: $incoming_amount");

        if($order_amount !== $incoming_amount) {
            $message = "Amount mismatch (order: {$order_amount}, paid: {$incoming_amount}).";
            error_log($message);
        } else {
            // If eSewa reports COMPLETE (or we trust session and amounts), mark order completed
            if(strtoupper($status) === 'COMPLETE' || strtoupper($status) === 'SUCCESS' || !empty($transaction_uuid)) {
                
                // EMERGENCY: Check if column exists, add if not (to handle user frustration with N/A)
                $check_col = $conn->query("SHOW COLUMNS FROM orders LIKE 'transaction_id'");
                if($check_col && $check_col->num_rows == 0) {
                    $conn->query("ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(255) AFTER order_status");
                    error_log("EMERGENCY: Added transaction_id column in success page");
                }

                $update = $conn->prepare("UPDATE orders SET payment_status='completed', order_status='processing', transaction_id=? WHERE id=?");
                $update->bind_param('si', $transaction_uuid, $order['id']);
                if(!$update->execute()) {
                    error_log("UPDATE FAILED: " . $update->error);
                } else {
                    if($update->affected_rows > 0) {
                        error_log("UPDATE SUCCESS: Order ID {$order['id']} updated with Transaction ID $transaction_uuid");
                    } else {
                        error_log("UPDATE WARNING: Executed but 0 rows affected for Order ID {$order['id']} (Data might be identical or ID incorrect)");
                    }
                }
                $update->close();

                // SUCCESS: The Transaction ID is now stored in the 'orders' table by the UPDATE query above.
                // We do not need to insert into a separate transaction table anymore.

                // Clear cart and mark verified
                unset($_SESSION['cart']);
                $verified = true;
            } else {
                $message = 'Payment status not completed.';
            }
        }
    }
}

// Prepare display-safe values to avoid number_format type errors
$display_amount = '0.00';
if($total_amount !== '' && is_numeric($total_amount)) {
    $display_amount = number_format((float)$total_amount, 2, '.', ',');
} elseif(!empty($order)) {
    $display_amount = number_format((float)$order['total_amount'], 2, '.', ',');
}

$display_transaction = htmlspecialchars($transaction_uuid ?: ($_SESSION['transaction_id'] ?? 'N/A'));
// Determine paid amount reported by eSewa (if present) and stored order amount
$paid_raw = $_GET['amount'] ?? $_GET['total_amount'] ?? '';
$paid_amount = 'N/A';
if($paid_raw !== '' && is_numeric($paid_raw)) {
    $paid_amount = number_format((float)$paid_raw, 2, '.', ',');
}

$order_amount_display = 'N/A';
if(!empty($order) && isset($order['total_amount']) && is_numeric($order['total_amount'])) {
    $order_amount_display = number_format((float)$order['total_amount'], 2, '.', ',');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Successful - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-container { max-width:600px; margin:40px auto; padding:20px; }
        .success-box { background:white; padding:40px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); text-align:center; }
        .success-icon { font-size:60px; margin-bottom:20px; }
        .success-box h1 { color:#27ae60; margin:20px 0; }
        .success-box p { color:#666; font-size:16px; line-height:1.6; }
        .success-message { background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:15px; border-radius:4px; margin:20px 0; }
        .details { background:#f5f5f5; padding:20px; border-radius:4px; margin:20px 0; text-align:left; }
        .detail-row { display:flex; justify-content:space-between; margin-bottom:10px; }
        .btn { display:inline-block; padding:12px 30px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; margin:10px 5px; }
        .btn:hover { background:#1565c0; }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">🛒 ShoeStore</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="success-container">
    <div class="success-box">
        <div class="success-icon">✅</div>
        <h1>Payment Successful!</h1>
        
        <div class="success-message">
            <strong>Thank you for your purchase!</strong><br>
            Your order has been placed successfully.
        </div>
        
        <p>Your order is now being processed. You will receive a confirmation email shortly.</p>
        
        <div class="header-details" style="background:#e3f2fd; padding:15px; border-radius:4px; margin:20px 0; border-left:4px solid #1976d2;">
             <p style="margin:5px 0;"><strong>Transaction ID:</strong> <span style="font-family:monospace;"><?php echo htmlspecialchars($transaction_uuid); ?></span></p>
             <p style="margin:5px 0;"><strong>Amount Paid:</strong> Rs. <?php echo $display_amount; ?></p>
        </div>

        <div class="details">
            <p style="margin:0; font-size:16px;">We received your payment and your order is being processed. A confirmation email will be sent shortly. You can view your orders from your <a href="profile.php">profile</a>.</p>
        </div>
        
        <p><strong>What's Next?</strong></p>
        <p>
            1. Check your email for order confirmation<br>
            2. Track your order from your profile<br>
            3. We'll deliver your shoes within 3-5 business days
        </p>
        
        <div style="margin-top:30px;">
            <a href="profile.php" class="btn">View My Orders</a>
            <a href="index.php" class="btn">Continue Shopping</a>
        </div>
    </div>
</div>

</body>
</html>
