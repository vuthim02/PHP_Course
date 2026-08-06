<?php
// Chapter 08 — Working With Strings (video 44:27)
$phrase = "Giraffe Academy";

echo $phrase;
echo "<br>";
echo strtolower($phrase);
echo "<br>";
echo strtoupper($phrase);
echo "<br>";
echo strtoupper("dog");
echo "<br>";
echo strlen($phrase);
echo "<br>";
echo $phrase[0];
echo "<br>";
echo $phrase[1];
echo "<br>";
echo "Mike"[0];
echo "<br>";
echo str_replace("Giraffe", "Panda", $phrase);
echo "<br>";
echo substr($phrase, 8);
echo "<br>";
echo substr($phrase, 8, 3);
echo "<br>";

$phrase[0] = "B";
echo $phrase;
echo "<br>";
?>
