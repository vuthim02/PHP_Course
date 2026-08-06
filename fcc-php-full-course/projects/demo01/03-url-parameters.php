<?php
// Chapter 13 — URL Parameters (video 1:28:59)
// A URL parameter is "basically just a value that we can tack on to the end
// of one of our URLs, which will pass a value into our PHP program."
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/projects/03-url-parameters.php?name=Mike&age=70
// (or click the link below — the values are passed straight in the URL)

echo '<a href="' . htmlspecialchars($_SERVER["PHP_SELF"] . "?name=Mike&age=70") . '">Pass ?name=Mike&amp;age=70 in the URL</a><br>';

echo ($_GET["name"] ?? "") . "<br>";
echo ($_GET["age"] ?? "");
?>
