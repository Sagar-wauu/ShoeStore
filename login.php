<?php
include 'dp.php';
include 'auth.php';

$err = '';
$next = $_GET['next'] ?? 'index.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    // Validation
    if($email === '') {
        $err = '❌ Email is required';
    } elseif($pass === '') {
        $err = '❌ Password is required';
    } elseif(!validate_email($email)) {
        $err = '❌ Invalid email format';
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if($row = $res->fetch_assoc()){
            // Debug: Check if password is bcrypt hash or plain text
            $is_hash = (strpos($row['password'], '$2y$') === 0);
            
            if($is_hash){
                // Use password_verify for bcrypt hashes
                $password_match = password_verify($pass, $row['password']);
            } else {
                // Fallback for plain text passwords (legacy)
                $password_match = ($pass === $row['password']);
            }
            
            if($password_match){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['is_admin'] = $row['is_admin'];
                
                // Redirect based on admin status
                if($row['is_admin'] == 1){
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $err = '❌ Invalid password';
            }
        } else {
            $err = '❌ No account found with this email';
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ShoeStore</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .error { color:#d32f2f; background:#ffebee; padding:10px; border-radius:5px; margin-bottom:15px; }
        .success { color:#388e3c; background:#e8f5e9; padding:10px; border-radius:5px; margin-bottom:15px; }
        a { color:#1976d2; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>🔐 Login to ShoeStore</h2>
    <?php if($err) echo "<div class='error'>$err</div>"; ?>
    
    <form method="POST">
        <input name="email" placeholder="Email address" type="email" required>
        <input name="password" placeholder="Password (min 6 chars)" type="password" required>
        <button type="submit" class="primary">Login</button>
    </form>
    
    <p>Don't have an account? <a href="register.php">Create one now</a></p>
    <p><a href="index.php">← Back to Home</a></p>
</div>
</body>
</html>

