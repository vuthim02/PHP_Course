<?php
// Chapter 16 — Using Checkboxes (video 1:50:26)
// Checkboxes let the user pick multiple fruits. The [] in name="fruits[]"
// tells PHP to collect ALL checked values into one array.
// In the video this form lives in site.php with action="site.php".
// Here the form posts to this file itself (PHP_SELF) so it runs standalone.
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/projects/05-checkboxes.php
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    Apples: <input type="checkbox" name="fruits[]" value="apples"><br>
    Oranges: <input type="checkbox" name="fruits[]" value="oranges"><br>
    Pears: <input type="checkbox" name="fruits[]" value="pears"><br>
    <input type="submit">
</form>
<?php
// $fruits is an array holding every fruit that was checked, in the order
// the user checked them. Unchecked boxes are simply not submitted.
$fruits = $_POST["fruits"] ?? [];

// Exactly as the video does it: print the first and second checked fruits.
echo ($fruits[0] ?? "") . "<br>";   // first fruit checked
echo ($fruits[1] ?? "") . "<br>";   // second fruit checked
?>
