<?php
// logout.php - Log out the user and redirect to home page
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: index.php');
exit();
?>
