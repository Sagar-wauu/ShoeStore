<?php
include 'dp.php';
include 'search_algo.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if(strlen($term) < 1) {
    echo json_encode([]);
    exit;
}

// 1. Fetch ALL distinct brands SORTED (Requirement for Binary Search)
$query = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC";
$result = $conn->query($query);

$brands = [];
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $brands[] = ['brand' => $row['brand']];
    }
}

// 2. Use Binary Search to find the first match
$startIndex = binarySearch($brands, $term);
$matches = [];

if ($startIndex != -1) {
    // Collect matches starting from the found index
    $term = strtolower($term);
    $n = count($brands);
    
    for($i = $startIndex; $i < $n; $i++) {
        $brandName = $brands[$i]['brand'];
        
        // Stop if it no longer matches the prefix
        if(strpos(strtolower($brandName), $term) !== 0) {
            break;
        }
        
        $matches[] = $brandName;
        
        // Limit to 5 suggestions
        if(count($matches) >= 5) break; 
    }
}

echo json_encode($matches);
?>
