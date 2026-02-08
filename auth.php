<?php
// auth.php - Basic authentication check for ShoeStore
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$public_pages = [
    'front.php', // home page
    'products.php', // product listing
    'product_details.php', // product details
    'login.php',
    'register.php',
    'styles.css',
    'script.js',
    'cart.css',
    // add more public files as needed
];
$current_page = basename($_SERVER['PHP_SELF']);
if (!in_array($current_page, $public_pages)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}


// Helper: check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
// Helper: check if user is admin
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Require user to be logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

// Require admin for admin pages
function require_admin() {
    if (!is_admin()) {
        header('Location: admin_dashboard.php');
        exit();
    }
}
?>
