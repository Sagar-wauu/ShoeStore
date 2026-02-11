<?php
include 'dp.php';
include 'auth.php';

require_admin();

$msg = '';
$action = $_GET['action'] ?? '';

// Delete user
if($action === 'delete' && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    // Don't allow deleting yourself
    if($user_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param('i', $user_id);
        if($stmt->execute()) {
            $msg = '✓ User deleted successfully!';
        } else {
            $msg = '❌ Error deleting user';
        }
        $stmt->close();
    } else {
        $msg = '❌ Cannot delete your own account';
    }
}

// Fetch all users with order count
$result = $conn->query("SELECT u.id, u.name, u.email, u.phone, u.city, u.is_admin, u.created_at, COUNT(o.id) as total_orders FROM users u LEFT JOIN orders o ON u.id = o.user_id GROUP BY u.id ORDER BY u.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Manage Users - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <style>
        .admin-container { max-width:1200px; margin:0 auto; padding:20px; }
        .message { padding:10px; border-radius:4px; margin-bottom:20px; }
        .message.success { background:#d4edda; color:#155724; }
        .message.error { background:#f8d7da; color:#721c24; }
        .table { width:100%; border-collapse:collapse; background:white; }
        .table th { background:#f5f5f5; padding:12px; text-align:left; border-bottom:2px solid #ddd; }
        .table td { padding:12px; border-bottom:1px solid #ddd; }
        .table tr:hover { background:#f9f9f9; }
        .badge { display:inline-block; padding:5px 10px; border-radius:4px; font-size:12px; font-weight:bold; }
        .admin-badge { background:#d4edda; color:#155724; }
        .user-badge { background:#e7f3ff; color:#0056b3; }
        .actions { display:flex; gap:5px; }
        .btn { display:inline-block; padding:5px 10px; background:#1976d2; color:white; text-decoration:none; border-radius:4px; font-size:12px; cursor:pointer; border:none; }
        .btn:hover { background:#1565c0; }
        .btn-danger { background:#d32f2f; }
        .btn-danger:hover { background:#c62828; }
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
    <h1>Manage Users</h1>
    
    <?php if($msg): ?>
        <div class="message <?php echo strpos($msg, '✓') === 0 ? 'success' : 'error'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Role</th>
                <th>Total Orders</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['city'] ?? '-'); ?></td>
                        <td>
                            <?php if($row['is_admin']): ?>
                                <span class="badge admin-badge">Admin</span>
                            <?php else: ?>
                                <span class="badge user-badge">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['total_orders']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <div class="actions">
                                <?php if($row['id'] !== $_SESSION['user_id']): ?>
                                    <a href="admin_users.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this user?');">Delete</a>
                                <?php else: ?>
                                    <span style="color:#999; font-size:12px;">-</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:40px; color:#999;">No users yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
