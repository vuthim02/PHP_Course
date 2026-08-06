<?php
// Chapter 25 — For Loops (video 3:15:18)

// 1. Basic for loop — packages init, condition, increment in one line
for ($i = 1; $i <= 5; $i++) {
    echo "for loop: $i<br>";
}
// Output: for loop: 1 .. 5

echo "<br>";

// 2. The same idea with a while loop for comparison
$index = 1;
while ($index <= 5) {
    echo "while loop: $index<br>";
    $index++;
}
// Same output, but takes 4 lines instead of 2

echo "<br>";

// 3. Modify the iterating variable — step by 2
for ($i = 1; $i <= 10; $i += 2) {
    echo "step 2: $i<br>";
}
// Output: 1, 3, 5, 7, 9

echo "<br>";

// 4. Loop through an array (the classic use case)
$luckyNumbers = array(4, 8, 15, 16, 23, 42);
for ($i = 0; $i < count($luckyNumbers); $i++) {
    echo "luckyNumbers[$i] = " . $luckyNumbers[$i] . "<br>";
}
// Output: 4, 8, 15, 16, 23, 42
// $i starts at 0 (arrays are 0-indexed) and uses < count, not <=

echo "<br>";

// 5. For-loop version of even numbers (if inside the loop)
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) {
        echo "even: $i<br>";
    }
}
// Output: even: 2, 4, 6, 8, 10
?>
