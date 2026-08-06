<?php
// Chapter 14 — POST vs GET (video 1:35:52)
// A password form. With method="post" the password is sent between the
// client and server "in a more secure fashion" — it NEVER appears in the URL.
// In the video this form lives in site.php with action="site.php".
// Here the form posts to this file itself (PHP_SELF) so it runs standalone.
// Run:  php -S localhost:4000 -t /home/tim-ham/Desktop/Learn_New_skill/WebDev/PHP_Course/fcc-php-full-course
// Then open:  http://localhost:4000/projects/04-post-vs-get.php
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    Password: <input type="password" name="password">
    <input type="submit">
</form>
<?php
// The superglobal must match the form's method: method="post" -> $_POST.
// (The video just writes: echo $_POST["password"];)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "Your password was: " . ($_POST["password"] ?? "");
}
?>
