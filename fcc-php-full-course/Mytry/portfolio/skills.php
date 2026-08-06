<?php
// skills.php — Shows the skills associative array with a stars() helper
include "data.php";
include "header.php";
?>

<h1>My Skills</h1>
<p>Skills are stored in an <em>associative array</em> (skill &rArr; level 1&ndash;5),
   and the <code>stars()</code> function draws the dots with a for loop.</p>

<?php foreach ($skills as $skill => $level) { ?>
  <div class="card">
    <h2><?php echo $skill; ?></h2>
    <p><?php echo stars($level); ?></p>
  </div>
<?php } ?>

<?php include "footer.php"; ?>
