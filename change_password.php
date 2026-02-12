<?php
include 'dp.php';
include 'auth.php';

require_login();

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    
    // Validation
    if(!$current_pass) {
        $err = '❌ Current password is required';
    } elseif(!$new_pass) {
        $err = '❌ New password is required';
    } elseif(!validate_password($new_pass)) {
        $err = '❌ New password must be at least 6 characters';
    } elseif($new_pass !== $confirm_pass) {
        $err = '❌ Passwords do not match';
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if(!password_verify($current_pass, $user['password'])) {
            $err = '❌ Current password is incorrect';
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param('si', $new_hash, $_SESSION['user_id']);
            
            if($stmt->execute()) {
                $success = '✓ Password changed successfully!';
            } else {
                $err = '❌ Error changing password';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - ShoeStore</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container { max-width:500px; margin:40px auto; padding:20px; }
        .card { background:white; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom:20px; }
        label { display:block; margin-bottom:5px; font-weight:bold; }
        input { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box; }
        .error { background:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; }
        .success { background:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; }
        .btn { display:inline-block; padding:10px 20px; background:#1976d2; color:white; border:none; border-radius:4px; cursor:pointer; }
        .btn:hover { background:#1565c0; }
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
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <div class="back-link"><a href="profile.php">← Back to Profile</a></div>
    
    <div class="card">
        <h1>🔐 Change Password</h1>
        
        <?php if($err): ?>
            <div class="error"><?php echo $err; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Current Password *</label>
                <input type="password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label>New Password *</label>
                <input type="password" name="new_password" required placeholder="Min 6 characters">
            </div>
            
            <div class="form-group">
                <label>Confirm New Password *</label>
                <input type="password" name="confirm_password" required placeholder="Re-enter new password">
            </div>
            
            <button type="submit" class="btn">Change Password</button>
        </form>
    </div>
</div>

</body>
</html>
