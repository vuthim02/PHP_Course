<?php
// Chapter 21 — If Statements (continued): Comparisons (video 2:37:16)

// 1. The built-in max function
echo "max(3, 6) = " . max(3, 6) . "<br>";   // 6

// 2. Write our own getMax using a comparison
function getMax($num1, $num2) {
    if ($num1 > $num2) {
        return $num1;
    } else {
        return $num2;
    }
}
echo "getMax(3, 90) = " . getMax(3, 90) . "<br>";    // 90
echo "getMax(300, 90) = " . getMax(300, 90) . "<br>"; // 300

// 3. getMax with three numbers (process of elimination)
function getMax3($num1, $num2, $num3) {
    if ($num1 >= $num2 && $num1 >= $num3) {
        return $num1;
    } elseif ($num2 >= $num1 && $num2 >= $num3) {
        return $num2;
    } else {
        return $num3;
    }
}
echo "getMax3(300, 900, 400) = " . getMax3(300, 900, 400) . "<br>";    // 900
echo "getMax3(3000, 900, 400) = " . getMax3(3000, 900, 400) . "<br>";   // 3000
echo "getMax3(3000, 900, 3000) = " . getMax3(3000, 900, 3000) . "<br>"; // 3000 (tie)

// 4. Every comparison operator, each inside an if statement
$num1 = 10;
$num2 = 5;

if ($num1 > $num2)  { echo "$num1 > $num2 is TRUE<br>"; }    // greater than
if ($num1 < $num2)  { echo "$num1 < $num2 is TRUE<br>"; }    // less than
if ($num1 >= $num2) { echo "$num1 >= $num2 is TRUE<br>"; }   // greater than or equal
if ($num1 <= $num2) { echo "$num1 <= $num2 is TRUE<br>"; }   // less than or equal
if ($num1 == $num2) { echo "$num1 == $num2 is TRUE<br>"; }   // equal (double equals)
if ($num1 != $num2) { echo "$num1 != $num2 is TRUE<br>"; }   // not equal

// 5. Comparing strings with ==
if ("Mike" == "Mike") {
    echo "Strings are equal<br>";
}

// 6. && (AND) — both sides must be true
$isRaining = false;
if ($num1 > 7 && $isRaining == false) {
    echo "AND: both conditions are true<br>";
}

// 7. || (OR) — only one side needs to be true
if ($num1 < 5 || $isRaining) {
    echo "OR: at least one condition is true<br>";
}

// 8. A full decision example combining the operators
$temp = 82;      // degrees Fahrenheit
$isRaining = false;

if ($temp > 90 && !$isRaining) {
    echo "It's blazing hot and dry — stay in the shade<br>";
} elseif ($temp > 80 || $isRaining) {
    echo "It's warm enough to go outside — grab a drink<br>";
} elseif ($temp <= 50) {
    echo "It's cold — bring a jacket<br>";
} else {
    echo "It's a perfect temperature<br>";
}
?>
