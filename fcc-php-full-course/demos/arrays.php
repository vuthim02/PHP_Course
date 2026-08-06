<?php
// Chapter 15 — Arrays (video 1:41:44)
// An array is "very similar to a variable but, unlike a variable,
// an array can store more than one piece of information."
// Run: php demos/arrays.php   (or open it in the browser)

// --- Creating an array ---
$friends = array("Kevin", "Karen", "Oscar", "Jim");

// Echoing the whole array just prints "Array" (and warns on PHP 8).
// print_r() / var_dump() show what's actually inside:
print_r($friends);
echo "<br>";

// --- Accessing elements by index (indexing starts at ZERO) ---
echo $friends[0] . "<br>";   // Kevin  (first element = index 0)
echo $friends[1] . "<br>";   // Karen  (second element = index 1)
echo $friends[2] . "<br>";   // Oscar

// --- Modifying an element ---
$friends[1] = "Dwight";
echo $friends[1] . "<br>";   // Dwight (Karen is replaced)

// --- Storing different data types side by side ---
$friends[3] = 400;           // a number among strings
echo $friends[3] . "<br>";   // 400

// --- Adding an element at any index ---
$friends[4] = "Angela";
echo $friends[4] . "<br>";   // Angela (added onto the end)

// --- Counting the elements with count() ---
echo "count: " . count($friends) . "<br>";   // 5 (indices 0-4)

// Adding another element bumps the count:
$friends[5] = "Mike";
echo "count: " . count($friends) . "<br>";   // 6
?>
