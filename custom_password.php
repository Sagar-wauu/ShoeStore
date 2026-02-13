<?php

function generateSalt($length = 16) {
    return bin2hex(random_bytes($length)); // 32 hex chars
}
function customHashPassword($password, $salt = null) {
    if (!$salt) $salt = generateSalt(16); // 32 hex chars
    $combined = $salt . $password;
    $hash = hash('sha256', $combined);
    for ($i = 0; $i < 1000; $i++) {
        $hash = hash('sha256', $hash . $combined);
    }
    $finalHash = substr($hash, 0, 28); // 28 chars
    return $salt . $finalHash; // 32 + 28 = 60 chars
}
function verifyCustomPassword($password, $stored) {
    $salt = substr($stored, 0, 32); // first 32 chars is salt
    return customHashPassword($password, $salt) === $stored;
}
?>