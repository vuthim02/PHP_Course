<?php
// 404.php — page not found.
$pageTitle = "Page Not Found";
include __DIR__ . "/layout/header.php";
?>

<div class="amz-404">
  <h1>Page not found</h1>
  <p>We couldn't find the page you're looking for.</p>
  <a class="amz-btn amz-btn-yellow" href="/">Go to Home</a>
</div>

<?php include __DIR__ . "/layout/footer.php"; ?>
