<?php
include 'dp.php';
include 'auth.php';

// Add to cart
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)($_POST['qty'] ?? 1);
    
    // Get product with stock validation
    $stmt = $conn->prepare("SELECT id, name, price, image, quantity FROM products WHERE id=?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    // Validate stock availability
    if(!$product) {
        // Product doesn't exist
        $_SESSION['cart_error'] = '❌ Product not found';
    } elseif($qty <= 0) {
        // Invalid quantity
        $_SESSION['cart_error'] = '❌ Invalid quantity';
    } elseif($product['quantity'] <= 0) {
        // Out of stock
        $_SESSION['cart_error'] = '❌ Product is out of stock';
    } elseif($qty > $product['quantity']) {
        // Requested quantity exceeds available stock
        $_SESSION['cart_error'] = '❌ Only ' . $product['quantity'] . ' unit(s) available in stock';
    } else {
        // All validations passed - add to cart
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product already in cart
        $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['qty'] : 0;
        $total_qty = $current_qty + $qty;
        
        // Validate total quantity doesn't exceed stock
        if($total_qty > $product['quantity']) {
            $_SESSION['cart_error'] = '❌ Cannot add more. Only ' . $product['quantity'] . ' unit(s) available. You already have ' . $current_qty . ' in cart.';
        } else {
            // Add to cart
            if(isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['qty'] += $qty;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'qty' => $qty
                ];
            }
            
            // Clear any previous error
            unset($_SESSION['cart_error']);
            $_SESSION['cart_success'] = '✅ ' . $product['name'] . ' added to cart!';
        }
    }
    
    header('Location: cart.php');
    exit;
}

// Remove item
if(isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: cart.php');
    exit;
}

// Update quantity
if(isset($_POST['update_qty'])) {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];
    
    if($qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['cart_success'] = '✅ Item removed from cart';
    } else {
        // Validate stock before updating
        $stmt = $conn->prepare("SELECT quantity FROM products WHERE id=?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stock_result = $stmt->get_result();
        $stock = $stock_result->fetch_assoc();
        $stmt->close();
        
        if(!$stock || $stock['quantity'] <= 0) {
            $_SESSION['cart_error'] = '❌ Product is out of stock';
        } elseif($qty > $stock['quantity']) {
            $_SESSION['cart_error'] = '❌ Only ' . $stock['quantity'] . ' unit(s) available. Cannot update quantity.';
        } else {
            $_SESSION['cart'][$product_id]['qty'] = $qty;
            unset($_SESSION['cart_error']);
            $_SESSION['cart_success'] = '✅ Quantity updated';
        }
    }
    
    header('Location: cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - ShoeStore</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        .cart-container { max-width:1000px; margin:20px auto; padding:20px; }
        .cart-table { width:100%; border-collapse:collapse; background:white; margin-bottom:20px; }
        .cart-table th { background:#f5f5f5; padding:15px; text-align:left; border-bottom:2px solid #ddd; }
        .cart-table td { padding:15px; border-bottom:1px solid #ddd; }
        .cart-table tr:hover { background:#f9f9f9; }
        .product-cell { display:flex; gap:15px; align-items:center; }
        .product-image { width:80px; height:80px; object-fit:cover; border-radius:4px; }
        .qty-input { width:60px; padding:5px; border:1px solid #ddd; border-radius:4px; }
        .remove-btn { color:#d32f2f; cursor:pointer; text-decoration:none; }
        .remove-btn:hover { text-decoration:underline; }
        .cart-summary { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); max-width:400px; margin-left:auto; }
        .summary-row { display:flex; justify-content:space-between; margin-bottom:15px; padding-bottom:15px; border-bottom:1px solid #eee; }
        .summary-row.total { font-size:20px; font-weight:bold; border-bottom:2px solid #ddd; padding-bottom:10px; }
        .btn { display:inline-block; padding:12px 30px; background:#1976d2; color:white; text-decoration:none; border:none; border-radius:4px; cursor:pointer; font-size:16px; }
        .btn:hover { background:#1565c0; }
        .btn-secondary { background:#666; }
        .btn-secondary:hover { background:#555; }
        .buttons { display:flex; gap:10px; margin-top:20px; }
        .empty-message { text-align:center; padding:40px; background:white; border-radius:8px; }
        .empty-message a { color:#1976d2; }
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

<div class="cart-container">
    <h1>🛒 Shopping Cart</h1>
    
    <?php if(isset($_SESSION['cart_error'])): ?>
        <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:4px; margin-bottom:20px; border-left:4px solid #d32f2f;">
            <?php echo $_SESSION['cart_error']; ?>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['cart_success'])): ?>
        <div style="background:#d4edda; color:#155724; padding:12px; border-radius:4px; margin-bottom:20px; border-left:4px solid #28a745;">
            <?php echo $_SESSION['cart_success']; ?>
        </div>
        <?php unset($_SESSION['cart_success']); ?>
    <?php endif; ?>
    
    <?php if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <div class="empty-message">
            <h2>Your cart is empty</h2>
            <p>Start shopping to add items to your cart!</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:center;">Quantity</th>
                    <th style="text-align:right;">Total</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grand_total = 0;
                foreach($_SESSION['cart'] as $id => $item): 
                    $total = $item['price'] * $item['qty'];
                    $grand_total += $total;
                ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="Product images/<?php echo htmlspecialchars($item['image']); ?>" alt="Product" class="product-image">
                                <div>
                                    <h4 style="margin:0;"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <small style="color:#666;">Product ID: <?php echo $id; ?></small>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:right;"><strong>Rs. <?php echo number_format($item['price'], 2); ?></strong></td>
                        <td style="text-align:center;">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="update_qty" value="1">
                                <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" max="100" class="qty-input" onchange="this.form.submit();">
                            </form>
                        </td>
                        <td style="text-align:right;"><strong>Rs. <?php echo number_format($total, 2); ?></strong></td>
                        <td style="text-align:center;">
                            <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn" onclick="return confirm('Remove from cart?');">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rs. <?php echo number_format($grand_total, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>Rs. 50.00</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>Rs. <?php echo number_format($grand_total + 50, 2); ?></span>
            </div>
            
            <div class="buttons">
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                <?php if(is_logged_in()): ?>
                    <a href="checkout.php" class="btn">Proceed to Checkout →</a>
                <?php else: ?>
                    <a href="login.php?next=checkout.php" class="btn">Login to Checkout</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</html>
