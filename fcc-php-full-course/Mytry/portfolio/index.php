<?php
// index.php — Home / About Me page
include "data.php";
include "header.php";
?>

<div class="card">
  <h1><?php echo $myName; ?></h1>
  <p><strong><?php echo $myRole; ?></strong> &mdash; <?php echo $myLocation; ?></p>
  <p><?php echo $myBio; ?></p>
  <p>Email me at <a href="mailto:<?php echo $myEmail; ?>"><?php echo $myEmail; ?></a></p>
</div>

<div class="card">
  <h2>What I do</h2>
  <p>
    I build web pages with HTML, style them with CSS and make them
    interactive with <strong>PHP</strong> &mdash; variables, arrays, functions,
    classes and forms.
  </p>
  <p>Check out my <a href="projects.php">projects</a> below.</p>
</div>

<?php include "footer.php"; ?>
