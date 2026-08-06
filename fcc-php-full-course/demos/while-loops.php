<?php
// Chapter 24 — While Loops (video 3:05:09)

// 1. Count to 5 with a while loop
$index = 1;
while ($index <= 5) {
    echo "while loop: $index<br>";
    $index++;
}
// Output: while loop: 1 .. 5

echo "<br>";

// 2. Same loop WITHOUT the increment = infinite loop (try it, then Ctrl+C).
//    $index would stay 1 forever and the condition never becomes false.
// $index = 1;
// while ($index <= 5) {
//     echo "while loop: $index<br>";
// }

// 3. do-while: body runs first, condition checked after
//    With $index = 6 the while condition (<= 5) is false,
//    but the do-while body still runs at least once.
$index = 6;
do {
    echo "do while loop: $index<br>";
    $index++;
} while ($index <= 5);
// Output: do while loop: 6

echo "<br>";

// 4. An if statement INSIDE the loop — print only even numbers
$index = 1;
while ($index <= 10) {
    if ($index % 2 == 0) {
        echo "even: $index<br>";
    }
    $index++;
}
// Output: even: 2, 4, 6, 8, 10
?>
