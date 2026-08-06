<?php
// Chapter 19 — Return Statements (video 2:12:10)

// 1. Return a value from a function
function cube($num) {
    return $num * $num * $num;
}

// Store the returned value in a variable
$cubeResult = cube(4);
echo $cubeResult . "<br>";          // 64

// Cut out the middleman — echo the function call directly
echo cube(4) . "<br>";              // 64

// 2. Code after return never runs (dead code)
function cubeWithDeadCode($num) {
    return $num * $num * $num;
    echo "Hello";                   // NEVER executes
}
echo cubeWithDeadCode(4) . "<br>";  // 64, no "Hello"

// 3. Code BEFORE return does run
function cubeWithEchoFirst($num) {
    echo "About to cube $num<br>";
    return $num * $num * $num;
}
echo cubeWithEchoFirst(4) . "<br>"; // prints message, then 64

// 4. Return any type — strings and arrays work too
function favoriteFruit() {
    return "mango";
}
echo favoriteFruit() . "<br>";       // mango

function favoriteNumbers() {
    return array(4, 8, 15, 16, 23, 42);
}
$nums = favoriteNumbers();
echo $nums[0] . "<br>";              // 4

// 5. Bare `return;` — break out early, return nothing
function maybeCube($num) {
    if ($num < 0) {
        return;                     // exit early, no value
    }
    return $num * $num * $num;
}
echo "cube(4) = " . maybeCube(4) . "<br>";  // 64
echo "cube(-2) = " . maybeCube(-2) . "<br>"; // empty (null)
?>
