<?php
// Chapter 12 — Building a Mad Libs Game (video 1:22:13)
// Enter a color, a plural noun, and a celebrity; they get sprinkled
// into the classic poem: "Roses are red, violets are blue, I love you."
// In the video this form lives in site.php with action="site.php".
// Here the form posts to this file itself (PHP_SELF) so it runs standalone.
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/projects/02-mad-libs-game.php
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
    Color: <input type="text" name="color"><br>
    Plural Noun: <input type="text" name="pluralNoun"><br>
    Celebrity: <input type="text" name="celebrity"><br>
    <input type="submit">
</form>
<?php
// Store the submitted words in variables first...
$color      = $_GET["color"] ?? "";
$pluralNoun = $_GET["pluralNoun"] ?? "";
$celebrity  = $_GET["celebrity"] ?? "";

// ...then print them inside the story. The variable names must match
// the input names above. The ?? "" defaults stop warnings before submit.
echo "Roses are $color<br>";
echo "$pluralNoun are blue<br>";
echo "I love $celebrity<br>";
?>
