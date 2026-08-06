<?php
// Chapter 28 — Include: PHP (video 3:36:51)
// Pattern 1: a template file populated with variables the including page assigns
$title = "My First Post";
$author = "Mike";
$wordCount = 400;
include "article-header.php";

echo "<br><br>";

// Pattern 2: a utility file whose function and variable we can use
include "useful-tools.php";
sayHi("Mike");
echo $feetInMile;
?>
