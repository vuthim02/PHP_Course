<?php
// Chapter 23 — Switch Statements (video 2:56:53)
// HOW TO RUN: from the course root, run:  php -S localhost:4000
// then open http://localhost:4000/projects/08-grade-switch.php
// Enter a grade (A/B/C/D/F) and click Submit. GET param works too:
// .../08-grade-switch.php?grade=A   -> "You did amazing"
?>
<!DOCTYPE html>
<html>
<head>
    <title>Grade Checker</title>
</head>
<body>
<form action="08-grade-switch.php" method="get">
    Enter your grade: <input type="text" name="grade">
    <input type="submit">
</form>

<?php
$grade = $_GET["grade"];

switch ($grade) {
    case "A":
        echo "You did amazing";
        break;
    case "B":
        echo "You did pretty good";
        break;
    case "C":
        echo "You did poorly";
        break;
    case "D":
        echo "You did very bad";
        break;
    case "F":
        echo "You fail";
        break;
    default:
        echo "Invalid Grade";
}
?>
</body>
</html>
