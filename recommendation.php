<?php

include_once 'dp.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Content-based filtering: Recommend by category (PHP filtering)
function get_category_recommendations($category, $exclude_id = null, $count = 4) {
    global $conn;
    $all_products = [];
    $result = $conn->query("SELECT * FROM products");
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $all_products[] = $row;
        }
    }
    // Filter by category and exclude current product
    $filtered = array_filter($all_products, function($product) use ($category, $exclude_id) {
        if ($exclude_id !== null && $product['id'] == $exclude_id) return false;
        return isset($product['category']) && $product['category'] === $category;
    });
    // Shuffle and return up to $count
    $filtered = array_values($filtered); // reindex
    shuffle($filtered);
    return array_slice($filtered, 0, min($count, count($filtered)));
}

