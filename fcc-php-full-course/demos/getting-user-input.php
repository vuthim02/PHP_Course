<?php
// Chapter 10 — Getting User Input (video 1:05:14)
// The form is "the middleman between HTML and PHP" — it's where HTML and PHP meet.
// In the video this code lives in site.php and the form's action is "site.php".
// Here the form posts to this file itself (PHP_SELF) so it runs standalone.
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/demos/getting-user-input.php?name=Mike&age=30

// --- The HTML form (echoed so the file is pure PHP) ---
echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="get">
    Name: <input type="text" name="name"><br>
    Age: <input type="number" name="age"><br>
    <input type="submit">
</form><br>';

// --- The PHP handler ---
// The name used in the HTML must match the key used here.
echo "Your name is: " . ($_GET["name"] ?? "") . "<br>";
echo "Your age is: " . ($_GET["age"] ?? "");
?>
