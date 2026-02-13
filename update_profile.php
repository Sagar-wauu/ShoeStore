<?php
include 'dp.php';
include 'auth.php';

require_login();

$user_id = $_SESSION['user_id'];
$err = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'Nepal');
    
    // Validation
    if(!$name) {
        $err = '❌ Name is required';
    } elseif(strlen($name) < 3) {
        $err = '❌ Name must be at least 3 characters';
    } elseif($phone && !validate_phone($phone)) {
        $err = '❌ Invalid phone number format';
    } else {
        $stmt = $conn->prepare("
            UPDATE users 
            SET name=?, phone=?, address=?, city=?, postal_code=?, country=?
            WHERE id=?
        ");
        $stmt->bind_param('ssssssi', $name, $phone, $address, $city, $postal_code, $country, $user_id);
        
        if($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $success = '✓ Profile updated successfully!';
        } else {
            $err = '❌ Error updating profile. Please try again.';
        }
        $stmt->close();
    }
}

// Redirect back to profile with message
if($err) {
    $_SESSION['error'] = $err;
} elseif($success) {
    $_SESSION['success'] = $success;
}

header('Location: profile.php');
exit;
?>
