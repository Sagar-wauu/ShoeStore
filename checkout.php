<?php
// checkout.php - Modern layout with shipping address, payment, and order summary
include 'dp.php';
include 'auth.php';
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 50;
$total = $subtotal + $shipping;

$err = '';
$success = '';

// Pre-fill user info if available
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal = trim($_POST['postal'] ?? '');
    $payment = $_POST['payment'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $address === '' || $city === '' || $postal === '' || $payment === '') {
        $err = 'Please fill all required fields and select a payment method.';
    } elseif (!$phone) {
        $err = '❌ Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $err = '❌ Phone number must be exactly 10 digits (no letters or special characters).';
    } elseif (!$payment) {
        $err = 'Please select a payment method.';
    } elseif ($payment === 'cod') {
        // Place order as Cash on Delivery (add DB logic here)
        $success = 'Order placed successfully! You chose Cash on Delivery.';
        // Optionally clear cart: unset($_SESSION['cart']);
    } elseif ($payment === 'esewa') {
        // Redirect to Esewa payment page
        header('Location: esewa_payment.php?amount=' . $total);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { background: #f5f5f5; }
        .checkout-main { display: flex; gap: 40px; max-width: 1200px; margin: 40px auto; }
        .checkout-left { flex: 2; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
        .checkout-right { flex: 1; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
        .checkout-title { font-size: 2rem; font-weight: bold; margin-bottom: 30px; color: #222; }
        .section-title { font-size: 1.2rem; font-weight: bold; color: #1976d2; margin-bottom: 18px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 6px; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; font-size: 15px; }
        .form-row { display: flex; gap: 18px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }
        .payment-methods { margin: 30px 0; }
        .pay-option { background: #f0f7ff; border: 2px solid #1976d2; border-radius: 8px; padding: 18px 24px; cursor: pointer; transition: box-shadow 0.2s, border 0.2s; text-align: left; margin-bottom: 12px; display: flex; align-items: center; }
        .pay-option.selected, .pay-option:hover { box-shadow: 0 4px 16px rgba(25,118,210,0.08); border: 2px solid #1565c0; }
        .pay-option input[type=radio] { margin-right: 16px; }
        .pay-option label { font-size: 18px; color: #1976d2; font-weight: bold; cursor: pointer; margin-bottom: 0; }
        .pay-option .icon { font-size: 28px; margin-right: 16px; }
        .checkout-btn { width: 100%; padding: 15px; background: #1976d2; color: #fff; border: none; border-radius: 5px; font-size: 18px; font-weight: bold; margin-top: 20px; transition: background 0.2s; }
        .checkout-btn:hover { background: #1565c0; }
        .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success { color: #388e3c; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .order-summary-title { font-size: 1.2rem; font-weight: bold; color: #222; margin-bottom: 18px; }
        .summary-list { margin-bottom: 18px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 16px; }
        .summary-item .name { color: #222; }
        .summary-item .qty { color: #555; }
        .summary-item .price { color: #d32f2f; font-weight: bold; }
        .summary-total { font-size: 20px; font-weight: bold; color: #1976d2; display: flex; justify-content: space-between; margin-top: 18px; }
        .edit-cart-link { color: #1976d2; text-decoration: underline; font-size: 15px; margin-top: 10px; display: inline-block; }
        @media (max-width: 900px) {
            .checkout-main { flex-direction: column; gap: 20px; }
        }
    </style>
</head>
<body>
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
<div class="checkout-main">
    <div class="checkout-left">
        <div class="checkout-title">Checkout</div>
        <?php if($err) echo "<div class='error'>$err</div>"; ?>
        <?php if($success) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <div class="section-title">Shipping Address</div>
            <div class="form-group">
                <label>Full Name</label>
                <input name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input name="email" type="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label for="phone" style="font-weight:bold;">Phone Number *</label>
                <input type="text" name="phone" id="phone" maxlength="10" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($phone); ?>" placeholder="98XXXXXXXX" required oninput="validatePhone()" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;">
                <div id="phoneError" class="error" style="display:none;margin-top:5px;padding:5px 10px;">❌ Phone number must be exactly 10 digits</div>
            </div>
            <div class="form-group">
                <label>Street Address *</label>
                <textarea name="address" rows="2" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>City *</label>
                    <input name="city" type="text" required>
                </div>
                <div class="form-group">
                    <label>Postal Code *</label>
                    <input name="postal" type="text" required>
                </div>
            </div>
            <div class="section-title">Payment Method</div>
            <div class="payment-methods">
                <div class="pay-option">
                    <input type="radio" id="cod" name="payment" value="cod">
                    <span class="icon">💵</span>
                    <label for="cod">Cash on Delivery (COD)</label>
                    <div style="font-size:14px; color:#555; margin-top:4px;">Pay when your order arrives</div>
                </div>
                <div class="pay-option">
                    <input type="radio" id="esewa" name="payment" value="esewa">
                    <span class="icon"><img src="https://esewa.com.np/common/images/esewa_logo.png" alt="eSewa" style="height:28px;vertical-align:middle;"></span>
                    <label for="esewa">eSewa Payment</label>
                    <div style="font-size:14px; color:#555; margin-top:4px;">Secure online payment via eSewa</div>
                </div>
            </div>
            <button type="submit" class="checkout-btn">Place Order</button>
        </form>
    </div>
    <div class="checkout-right">
        <div class="order-summary-title">Order Summary</div>
        <div class="summary-list">
            <?php foreach($cart as $item): ?>
                <div class="summary-item">
                    <span class="name"><?php echo htmlspecialchars($item['name']); ?></span>
                    <span class="qty">x<?php echo $item['qty']; ?></span>
                    <span class="price">Rs. <?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="summary-item">
            <span>Subtotal</span>
            <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-item">
            <span>Shipping</span>
            <span>Rs. <?php echo number_format($shipping, 2); ?></span>
        </div>
        <div class="summary-total">
            <span>Total</span>
            <span>Rs. <?php echo number_format($total, 2); ?></span>
        </div>
        <a href="cart.php" class="edit-cart-link">&larr; Edit Cart</a>
    </div>
</div>
<script>
        // Highlight selected payment option
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.pay-option');
            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    options.forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input[type=radio]').checked = true;
                });
            });
        });

        // Phone validation
        function validatePhone() {
            const phoneInput = document.getElementById('phone');
            const errorMsg = document.getElementById('phoneError');
            const phone = phoneInput.value;
            // Only allow digits
            const sanitized = phone.replace(/[^0-9]/g, '');
            if(sanitized !== phone) {
                phoneInput.value = sanitized;
            }
            // Check if length is exactly 10
            if(sanitized.length !== 10) {
                errorMsg.style.display = 'block';
                phoneInput.style.borderColor = '#d32f2f';
            } else {
                errorMsg.style.display = 'none';
                phoneInput.style.borderColor = '#ddd';
            }
        }
    </script>
</body>
</html>
