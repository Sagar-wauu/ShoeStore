<?php
// Binary Search to find the starting index of a prefix in a sorted array
function binarySearch(array $arr, string $elem): int { 
    $low = 0; 
    $high = count($arr) - 1; 
    $elem = strtolower($elem);
    $found_index = -1;

    // First find any occurrence or insertion point
    while ($low <= $high) { 
        $mid = intdiv($low + $high, 2); 
        $midVal = strtolower($arr[$mid]['brand']);
        
        // Check if midVal starts with elem
        if (strpos($midVal, $elem) === 0) {
            $found_index = $mid;
            $high = $mid - 1; // Try to find an earlier occurrence
        } elseif ($midVal < $elem) { 
            $low = $mid + 1; 
        } else { 
            $high = $mid - 1; 
        } 
    } 
    return $found_index; 
}
?>
