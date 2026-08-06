<?php
// Chapter 22 — Building a Better Calculator (video 2:47:13)
// HOW TO RUN: from the course root, run:  php -S localhost:4000
// then open http://localhost:4000/projects/07-better-calculator.php
// Pick two numbers and an operation, click Submit. GET params work too:
// .../07-better-calculator.php?num1=10&num2=5&op=add   -> 15
?>
<!DOCTYPE html>
<html>
<head>
    <title>Better Calculator</title>
</head>
<body>
<form action="07-better-calculator.php" method="get">
    First Num: <input type="number" step="0.1" name="num1"><br>
    Second Num: <input type="number" step="0.1" name="num2"><br>
    Operation:
    <select name="op">
        <option value="add">Add (+)</option>
        <option value="sub">Subtract (-)</option>
        <option value="mul">Multiply (*)</option>
        <option value="div">Divide (/)</option>
    </select>
    <input type="submit">
</form>

<?php
$num1 = $_GET["num1"];
$num2 = $_GET["num2"];
$op   = $_GET["op"];

if ($op == "add") {
    echo $num1 + $num2;
} elseif ($op == "sub") {
    echo $num1 - $num2;
} elseif ($op == "mul") {
    echo $num1 * $num2;
} elseif ($op == "div") {
    echo $num1 / $num2;
} else {
    echo "Invalid Operator";
}
?>
</body>
</html>
