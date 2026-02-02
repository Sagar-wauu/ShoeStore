<?php
include 'dp.php';
include 'auth.php';

require_login();

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_stmt = $conn->prepare("SELECT id, name, email, phone, address, city, postal_code, country FROM users WHERE id=?");
$user_stmt->bind_param('i', $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_stmt->close();

// Fetch user orders
$orders_stmt = $conn->prepare("
    SELECT id, total_amount, payment_status, order_status, payment_method, created_at, transaction_id
    FROM orders 
    WHERE user_id=? 
    ORDER BY created_at DESC
");
$orders_stmt->bind_param('i', $user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$orders_stmt->close();

// Get order count for stats
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id=?");
$count_stmt->bind_param('i', $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total_orders = $count_row['total'];
$count_stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-container { max-width:900px; margin:20px auto; padding:20px; }
        .nav-tabs { display:flex; gap:10px; margin-bottom:20px; border-bottom:2px solid #ddd; }
        .nav-tabs button {
            background:none;
            border:none;
            padding:10px 20px;
            cursor:pointer;
            font-size:16px;
            border-bottom:3px solid transparent;
        }
        .nav-tabs button.active { border-bottom-color:#1976d2; color:#1976d2; }
        .tab-content { display:none; }
        .tab-content.active { display:block; }
        .profile-card { background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
        .form-group { margin-bottom:15px; }
        label { display:block; margin-bottom:5px; font-weight:bold; }
        input, textarea { width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; }
        button { background:#1976d2; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; }
        button:hover { background:#1565c0; }
        .logout-btn { background:#d32f2f; }
        .logout-btn:hover { background:#c62828; }
        .stats-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px; }
        .stat-card { background:linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color:white; padding:20px; border-radius:8px; text-align:center; }
        .stat-card h3 { margin:0; font-size:32px; }
        .stat-card p { margin:5px 0 0 0; opacity:0.9; }
        .order-card {
            background:#f9f9f9;
            padding:15px;
            border-radius:8px;
            margin-bottom:15px;
            border-left:4px solid #1976d2;
            transition:all 0.3s ease;
        }
        .order-card:hover { box-shadow:0 2px 8px rgba(0,0,0,0.1); transform:translateY(-2px); }
        .order-header { display:flex; justify-content:space-between; align-items:start; margin-bottom:10px; }
        .order-id { font-size:18px; font-weight:bold; color:#1976d2; }
        .order-date { color:#666; font-size:14px; }
        .order-status {
            display:inline-block;
            padding:5px 10px;
            border-radius:4px;
            font-size:12px;
            font-weight:bold;
        }
        .status-pending { background:#fff3cd; color:#856404; }
        .status-completed { background:#d4edda; color:#155724; }
        .status-cancelled { background:#f8d7da; color:#721c24; }
        .status-cod { background:#cfe2ff; color:#084298; }
        .order-info { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:15px; margin:10px 0; }
        .info-item { }
        .info-label { font-size:12px; color:#999; text-transform:uppercase; }
        .info-value { font-size:14px; font-weight:bold; color:#333; }
        .order-items-preview { background:white; padding:10px; border-radius:4px; margin:10px 0; font-size:13px; }
        .item-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #eee; }
        .item-row:last-child { border-bottom:none; }
        .order-actions { margin-top:10px; }
        .order-actions a { display:inline-block; padding:8px 12px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; font-size:13px; margin-right:10px; }
        .order-actions a:hover { background:#1565c0; }
        .empty-state { text-align:center; padding:40px 20px; color:#999; }
        .empty-state a { color:#1976d2; }
        .back-link { margin-bottom:20px; }
        .back-link a { color:#1976d2; text-decoration:none; }
        .back-link a:hover { text-decoration:underline; }
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

<div class="profile-container">
    <div class="back-link"><a href="front.php">← Back to Home</a></div>
    
    <h1>👤 My Profile</h1>
    
    <div class="nav-tabs">
        <button class="tab-btn active" onclick="showTab('profile')">Profile Info</button>
        <button class="tab-btn" onclick="showTab('orders')">My Orders</button>
    </div>
    
    <!-- Profile Tab -->
    <div id="profile" class="tab-content active">
        <div class="profile-card">
            <h2>Profile Information</h2>
            <form method="POST" action="update_profile.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="98XXXXXXXX">
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" placeholder="Street address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" placeholder="City">
                </div>
                
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" placeholder="Postal Code">
                </div>
                
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" value="<?php echo htmlspecialchars($user['country'] ?? 'Nepal'); ?>" placeholder="Country">
                </div>
                
                <button type="submit">Update Profile</button>
            </form>
        </div>
        
        <div class="profile-card">
            <h2>Account Settings</h2>
            <p><a href="change_password.php">Change Password</a></p>
            <form method="POST" action="logout.php" style="margin-top:20px;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    
    <!-- Orders Tab -->
    <div id="orders" class="tab-content">
        <div class="profile-card">
            <h2>📦 Order History</h2>
            
            <!-- Order Stats -->
            <?php if($total_orders > 0): ?>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3><?php echo $total_orders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-card" style="background:linear-gradient(135deg, #27ae60 0%, #229954 100%);">
                        <h3>
                            <?php 
                            // Calculate total spent
                            $total_spent = 0;
                            $orders_result->data_seek(0); // Reset pointer
                            while($order = $orders_result->fetch_assoc()) {
                                $total_spent += $order['total_amount'];
                            }
                            echo 'Rs. ' . number_format($total_spent, 2);
                            ?>
                        </h3>
                        <p>Total Spent</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Orders List -->
            <?php if($orders_result->num_rows > 0): ?>
                <?php $orders_result->data_seek(0); // Reset pointer again ?>
                <?php while($order = $orders_result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Order #<?php echo $order['id']; ?></div>
                                <div class="order-date">📅 <?php echo date('M d, Y \a\t g:i A', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div style="text-align:right;">
                                <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                    <?php echo strtoupper($order['order_status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-info">
                            <div class="info-item">
                                <div class="info-label">Amount</div>
                                <div class="info-value">Rs. <?php echo number_format($order['total_amount'], 2); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Payment</div>
                                <div class="info-value">
                                    <?php 
                                    if($order['payment_method'] === 'esewa') {
                                        echo '💳 eSewa';
                                    } else if($order['payment_method'] === 'cod') {
                                        echo '💵 Cash on Delivery';
                                    } else {
                                        echo ucfirst($order['payment_method']);
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Status</div>
                                <div class="info-value" style="font-size:12px;">
                                    <?php 
                                    $status = strtolower($order['payment_status']);
                                    if($status === 'pending') echo '⏳ Pending';
                                    else if($status === 'completed') echo '✅ Completed';
                                    else if($status === 'failed') echo '❌ Failed';
                                    else echo ucfirst($status);
                                    ?>
                                </div>
                            </div>
                            <?php if(!empty($order['transaction_id'])): ?>
                            <div class="info-item">
                                <div class="info-label">Transaction ID</div>
                                <div class="info-value" style="font-size:12px; word-break:break-all;">
                                    <?php echo htmlspecialchars($order['transaction_id']); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <?php 
                        $items_stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id=? LIMIT 3");
                        $items_stmt->bind_param('i', $order['id']);
                        $items_stmt->execute();
                        $items_preview = $items_stmt->get_result();
                        $item_count = $items_preview->num_rows;
                        $all_items = $items_preview->fetch_all(MYSQLI_ASSOC);
                        $items_stmt->close();
                        ?>
                        
                        <?php if($item_count > 0): ?>
                            <div class="order-items-preview">
                                <strong style="color:#333;">Items:</strong>
                                <?php foreach($all_items as $item): ?>
                                    <div class="item-row">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                        <span>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <?php 
                                // Get total items in order
                                $total_items_stmt = $conn->prepare("SELECT SUM(quantity) as total FROM order_items WHERE order_id=?");
                                $total_items_stmt->bind_param('i', $order['id']);
                                $total_items_stmt->execute();
                                $total_items = $total_items_stmt->get_result()->fetch_assoc()['total'];
                                $total_items_stmt->close();
                                ?>
                                <?php if($total_items > 3): ?>
                                    <div style="text-align:center; padding-top:5px; color:#1976d2;">
                                        <small>... and <?php echo $total_items - 3; ?> more item(s)</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="order-actions">
                            <a href="order_details.php?id=<?php echo $order['id']; ?>">👁️ View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>📦 No Orders Yet</h3>
                    <p>You haven't placed any orders yet.</p>
                    <p><a href="products.php">Start Shopping Now →</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    const contents = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');
    
    contents.forEach(content => content.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));
    
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}
</script>

</body>
</html>
