<?php
include 'dp.php';
include 'auth.php';

require_admin();

$msg = '';
$action = $_GET['action'] ?? '';

// Update order status
if($action === 'update_status' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status = trim($_POST['status'] ?? '');
    
    if($status) {
        $stmt = $conn->prepare("UPDATE orders SET order_status=? WHERE id=?");
        $stmt->bind_param('si', $status, $order_id);
        if($stmt->execute()) {
            $msg = '✓ Order status updated!';
        }
        $stmt->close();
    }
}

// Fetch orders with customer info
$result = $conn->query("
    SELECT o.id, o.total_amount, o.order_status, o.payment_status, o.payment_method, o.shipping_address, o.user_id, o.transaction_id, u.name, u.email, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");

// Function to get order items
function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("
        SELECT oi.quantity, oi.price, p.name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin - Manage Orders - ShoeStore</title>
<link rel="stylesheet" href="styles.css">
<script src="script.js"></script>
<style>
    .admin-container { max-width:1200px; margin:0 auto; padding:20px; }
    .message { padding:10px; border-radius:4px; margin-bottom:20px; }
    .message.success { background:#d4edda; color:#155724; }
    .table { width:100%; border-collapse:collapse; background:white; }
    .table th { background:#f5f5f5; padding:12px; text-align:left; border-bottom:2px solid #ddd; }
    .table td { padding:12px; border-bottom:1px solid #ddd; vertical-align:top; }
    .table tr:hover { background:#f9f9f9; }
    .status-badge { display:inline-block; padding:5px 10px; border-radius:4px; font-size:12px; font-weight:bold; }
    .status-pending { background:#fff3cd; color:#856404; }
    .status-completed { background:#d4edda; color:#155724; }
    .status-cancelled { background:#f8d7da; color:#721c24; }
    .payment-pending { background:#ffeaa7; }
    .payment-completed { background:#a9dfbf; }
    select { padding:5px; border:1px solid #ddd; border-radius:4px; }
    .btn { padding:5px 10px; background:#1976d2; color:white; border:none; border-radius:4px; cursor:pointer; font-size:12px; }
    .btn:hover { background:#1565c0; }
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
    <h1>Manage Orders</h1>
    
    <?php if($msg): ?>
        <div class="message success"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Grand Total</th>
                <th>Payment Option</th>
                <th>Location</th>
                <th>Payment Status</th>
                <th>Order Date</th>
                <th>Name</th>
                <th>Order Status</th>
                <th>Order Item</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst($row['payment_method'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_address'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge payment-<?php echo strtolower($row['payment_status']); ?>">
                                <?php echo ucfirst($row['payment_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>
                            <form method="POST" action="admin_orders.php?action=update_status" style="display:flex; gap:5px;">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="status" onchange="this.form.submit();">
                                    <option value="pending" <?php echo $row['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $row['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="completed" <?php echo $row['order_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $row['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div style="font-size:12px;">
                                <?php 
                                $items = getOrderItems($conn, $row['id']);
                                while($item = $items->fetch_assoc()):
                                ?>
                                    <div>
                                        • <?php echo htmlspecialchars($item['name']); ?> 
                                        (x<?php echo $item['quantity']; ?>)
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </td>
                        <td>
                            <form action="order_details.php" method="get" style="margin:0;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-edit" style="font-size:12px;">View Details</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:40px; color:#999;">No orders yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
