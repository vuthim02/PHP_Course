<?php
// projects.php — Lists every project from the $projects array
include "data.php";
include "header.php";
?>

<h1>My Projects</h1>
<p>There are <?php echo count($projects); ?> projects in the array. Each one is a
   <code>Project</code> object with a title, a description and a URL.</p>

<?php foreach ($projects as $project) { ?>
  <div class="card">
    <h2><a class="project-link" href="<?php echo $project->url; ?>"><?php echo $project->title; ?></a></h2>
    <p><?php echo $project->description; ?></p>
  </div>
<?php } ?>

<?php include "footer.php"; ?>
