
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
    <input type="number" name="num1"><br>
    <input type="number" name="num2"><br>
    <input type="submit">
</form>
<?php
// Exactly as the video writes it (in site.php):
//   echo "Answer: " . ($_GET["num1"] + $_GET["num2"]);
// Because both inputs are type="number", PHP adds them as real numbers.
// The ?? 0 defaults just avoid warnings on the very first load.
$num1 = (int)($_GET["num1"] ?? 0);
$num2 = (int)($_GET["num2"] ?? 0);
echo "Answer: " . ($num1 + $num2);
?>
