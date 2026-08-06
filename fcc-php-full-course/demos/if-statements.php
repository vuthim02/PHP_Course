<?php
// Chapter 20 — If Statements (video 2:19:10)

// 1. Basic if with a boolean variable
$isMale = true;
if ($isMale) {
    echo "You are male<br>";
}
// Change to false and the line above is skipped

// 2. if / else
$isMale = false;
if ($isMale) {
    echo "You are male<br>";
} else {
    echo "You are not male<br>";
}

// 3. Two booleans and the && (AND) operator
$isMale = true;
$isTall = true;
if ($isMale && $isTall) {
    echo "You are a tall male<br>";   // both must be true
}

// 4. The || (OR) operator — only one needs to be true
$isMale = true;
$isTall = false;
if ($isMale || $isTall) {
    echo "You are a tall male (OR version)<br>";  // runs: isMale is true
}

// 5. Full if/elseif/else chain covering EVERY combination
$isMale = true;
$isTall = true;

if ($isMale && $isTall) {
    echo "You are a tall male<br>";
} elseif ($isMale && !$isTall) {
    echo "You are a short male<br>";
} elseif (!$isMale && $isTall) {
    echo "You are not male but are tall<br>";
} else {
    echo "You are not male and not tall<br>";
}

// Try the other three combinations by editing the two booleans above:
//   $isMale = true;  $isTall = false;  -> "You are a short male"
//   $isMale = false; $isTall = true;   -> "You are not male but are tall"
//   $isMale = false; $isTall = false;  -> "You are not male and not tall"
?>
