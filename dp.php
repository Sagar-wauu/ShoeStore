<?php
// Start session only if not already startedgit
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'shoestore';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($conn->connect_error) die("DB Error: ".$conn->connect_error);
$conn->set_charset('utf8mb4');

// Helper function for SQL injection prevention
function safe_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Helper function for validation
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10,}$/', preg_replace('/\D/', '', $phone));
}

function validate_password($pass) {
    return strlen($pass) >= 6;
}
?>
