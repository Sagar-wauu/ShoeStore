<?php
include 'dp.php';
include 'auth.php';

require_admin();

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin=0")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status='pending'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as sum FROM orders WHERE payment_status='completed'")->fetch_assoc()['sum'] ?? 0;

// Recent orders
$recent_orders = $conn->query("
    SELECT o.id, o.total_amount, o.order_status, u.name, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

// Orders in last 7, 15, 30 days
$orders_7 = $conn->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
$orders_15 = $conn->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 15 DAY)")->fetch_assoc()['count'];
$orders_30 = $conn->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        .admin-container { max-width:1200px; margin:0 auto; padding:20px; }
        .stat-card { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-left:4px solid #1976d2; }
        .stat-card h3 { margin:0 0 10px 0; color:#666; font-size:14px; }
        .stats-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); 
    gap: 20px;}
        .stat-value { font-size:32px; font-weight:bold; color:#1976d2; }
        .stat-card.revenue { border-left-color:#4caf50; }
        .stat-card.revenue .stat-value { color:#4caf50; }
        .stat-card.orders { border-left-color:#ff9800; }
        .stat-card.orders .stat-value { color:#ff9800; }
        .stat-card.pending { border-left-color:#f44336; }
        .stat-card.pending .stat-value { color:#f44336; }
        .recent-card { background:white; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .table { width:100%; border-collapse:collapse; }
        .table th { background:#f5f5f5; padding:12px; text-align:left; border-bottom:2px solid #ddd; }
        .table td { padding:12px; border-bottom:1px solid #ddd; }
        .table tr:hover { background:#f9f9f9; }
        .status-badge {
            display:inline-block;
            padding:5px 10px;
            border-radius:4px;
            font-size:12px;
            font-weight:bold;
        }
        .status-pending { background:#fff3cd; color:#856404; }
        .status-completed { background:#d4edda; color:#155724; }
        a { color:#1976d2; text-decoration:none; }
        a:hover { text-decoration:underline; }
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
    <h1>📊 Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $total_users; ?></div>
        </div> 
        
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="stat-value"><?php echo $total_products; ?></div>
        </div>
        
        <div class="stat-card orders">
            <h3>Total Orders</h3>
            <div class="stat-value"><?php echo $total_orders; ?></div>
        </div>
        
        <div class="stat-card pending">
            <h3>Pending Orders</h3>
            <div class="stat-value"><?php echo $pending_orders; ?></div>
        </div>
        
        <div class="stat-card revenue">
            <h3>Total Revenue</h3>
            <div class="stat-value">Rs. <?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>
    
    <div class="recent-card">
        <h2>Order Analysis </h2>
        <canvas id="ordersChart" width="400" height="180"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('ordersChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Last 7 Days', 'Last 15 Days', 'Last 30 Days'],
                    datasets: [{
                        label: 'Orders',
                        data: [<?php echo $orders_7; ?>, <?php echo $orders_15; ?>, <?php echo $orders_30; ?>],
                        backgroundColor: [
                            'rgba(25, 118, 210, 0.7)',
                            'rgba(255, 152, 0, 0.7)',
                            'rgba(76, 175, 80, 0.7)'
                        ],
                        borderColor: [
                            'rgba(25, 118, 210, 1)',
                            'rgba(255, 152, 0, 1)',
                            'rgba(76, 175, 80, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Days',
                                font: { size: 14, weight: 'bold' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            precision: 0,
                            title: {
                                display: true,
                                text: 'Orders',
                                font: { size: 14, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        </script>
        <h2>Recent Orders</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($row['order_status']); ?>">
                                <?php echo strtoupper($row['order_status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td><a href="admin_orders.php">View Details</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top:30px; padding:20px; background:#f5f5f5; border-radius:8px; text-align:center;">
        <p>Quick Links: <a href="admin_add_product.php">+ Add Product</a> | <a href="admin_orders.php">View All Orders</a> | <a href="admin_users.php">Manage Users</a></p>
    </div>
</div>

</body>
</html>
