<?php
// views/registry.php — wedding & baby registry info.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Registry</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Create a registry that has it all</h2>
    <p>Add anything from millions of products &mdash; electronics, books, kitchen
       gear and more. Share it with friends and family in one link.</p>
    <a class="amz-btn amz-btn-white" href="/register">Create your registry</a>
  </div>
  <img class="amz-banner-img" src="/img/categories/cat-home.jpg" alt="Registry" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128722;</div>
    <h3>Anything you want</h3>
    <p>Pick from millions of items at amazone prices.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128336;</div>
    <h3>Always up to date</h3>
    <p>Prices refresh automatically and sold-out items show suggestions.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128231;</div>
    <h3>One link to share</h3>
    <p>Guests buy straight from your registry without an account.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#127881;</div>
    <h3>Thank-you list</h3>
    <p>See who bought what and send a thank-you note.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>Ready to start?</h2>
  <p>Sign in or create a free account, then build your registry in minutes.</p>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/register">Get started</a>
  <a class="amz-btn amz-btn-white amz-btn-inline" href="/products">Browse products</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
