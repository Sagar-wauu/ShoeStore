<?php
include 'dp.php';
include 'auth.php';
require_once 'custom_password.php'; 

$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if(!$name) {
        $err = '❌ Full name is required';
    } elseif(strlen($name) < 3) {
        $err = '❌ Name must be at least 3 characters';
    } elseif(!$email) {
        $err = '❌ Email is required';
    } elseif(!validate_email($email)) {
        $err = '❌ Invalid email format';
    } elseif(!$pass) {
        $err = '❌ Password is required';
    } elseif(!validate_password($pass)) {
        $err = '❌ Password must be at least 6 characters';
    } elseif($pass !== $pass_confirm) {
        $err = '❌ Passwords do not match';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0) {
            $err = '❌ Email already registered. <a href="login.php">Login here</a>';
        } else {
            $hash = customHashPassword($pass); // $pass is the plain password
            $ins = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $name, $email, $hash);
            
            if($ins->execute()){
                $_SESSION['user_id'] = $ins->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['is_admin'] = 0;
                header('Location: index.php');
                exit;
            } else {
                $err = '❌ Error creating account. Please try again.';
            }
            $ins->close();
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
    <title>Register - ShoeStore</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .error { color:#d32f2f; background:#ffebee; padding:10px; border-radius:5px; margin-bottom:15px; }
        .success { color:#388e3c; background:#e8f5e9; padding:10px; border-radius:5px; margin-bottom:15px; }
        a { color:#1976d2; text-decoration:none; }
        a:hover { text-decoration:underline; }
        .password-requirements {
            font-size:12px;
            color:#666;
            margin-top:8px;
        }
    </style>
</head>
<body>
<div class="auth-box">
    <h2>📝 Create Account</h2>
    <?php if($err) echo "<div class='error'>$err</div>"; ?>
    <?php if($success) echo "<div class='success'>$success</div>"; ?>
    
    <form method="POST">
        <input name="name" placeholder="Full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
        <input name="email" placeholder="Email address" type="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        <input name="password" placeholder="Password (min 6 characters)" type="password" required>
        <input name="password_confirm" placeholder="Confirm password" type="password" required>
        
        <div class="password-requirements">
            ✓ Password must be at least 6 characters<br>
            ✓ Passwords must match
        </div>
        
        <button type="submit" class="primary">Create Account</button>
    </form>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>
    <p><a href="index.php">← Back to Home</a></p>
</div>
</body>
</html>
