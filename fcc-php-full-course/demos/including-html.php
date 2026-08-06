<?php
// Chapter 27 — Including HTML (video 3:31:08)
// HOW TO RUN: from the course root, run:  php -S localhost:4000
// then open http://localhost:4000/demos/including-html.php
// header.html supplies the doctype/head/title/opening body + brand h1.
// footer.html closes the page out. Your content goes in between.

include "header.html";
?>
<h1>Welcome</h1>
<p>Some article content goes here.</p>
<?php
include "footer.html";
?>
