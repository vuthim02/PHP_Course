<?php
// Chapter 17 — Associative Arrays (video 1:57:22)
// An associative array stores KEY-VALUE pairs (=>). The user types a
// student's name and we look up the grade that student got on the test.
// In the video this form lives in site.php with action="site.php".
// Here the form posts to this file itself (PHP_SELF) so it runs standalone.
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/projects/06-grade-lookup.php

// The associative array: key = student's name, value = test grade.
$grades = array("Jim" => "A+", "Pam" => "B-", "Oscar" => "C+");

// Direct access by key (no form needed):
echo "Jim's grade: " . $grades["Jim"] . "<br>";

// Modify a value by its key:
$grades["Jim"] = "F";           // Jim fails the test...
echo "Jim's new grade: " . $grades["Jim"] . "<br>";

// Keys must be unique; values may repeat.
echo "Number of students: " . count($grades) . "<br>";
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="text" name="student" placeholder="Student name">
    <input type="submit">
</form>
<?php
// Wire user input to the associative array: the submitted name becomes
// the key used for the lookup. (The video writes: echo $grades[$_POST["student"]];)
$name = $_POST["student"] ?? "";
echo $grades[$name] ?? "Not found";
?>
