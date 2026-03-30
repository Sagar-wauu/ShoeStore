<?php
// recommendation.php
// Algorithm: Random Recommendation
// This script fetches all products, shuffles them, and returns a random subset for recommendations.

include_once 'dp.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_random_recommendations($count = 4) {
    global $conn;
    $all_products = [];
    $result = $conn->query("SELECT * FROM products");
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $all_products[] = $row;
        }
        shuffle($all_products);
        return array_slice($all_products, 0, min($count, count($all_products)));
    }
    return [];
}
