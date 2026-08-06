<?php
// views/about.php — company story page.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">About amazone</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Earth's most customer-centric company</h2>
    <p>amazone started as a small idea and grew into a place where you can find
       anything you want to buy online &mdash; from books to electronics to camping gear.</p>
    <a class="amz-btn amz-btn-white" href="/products">Shop the store</a>
  </div>
  <img class="amz-banner-img" src="/img/categories/cat-books.jpg" alt="About amazone" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128230;</div>
    <h3>Millions of products</h3>
    <p>Books, electronics, home essentials, and everything in between.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128666;</div>
    <h3>Fast, FREE delivery</h3>
    <p>FREE delivery on orders over <?php echo money(25.0); ?>, right to your door.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#11088;</div>
    <h3>Customer-first</h3>
    <p>Easy returns, secure payments, and support that shows up.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128200;</div>
    <h3>Always innovating</h3>
    <p>From search to checkout, every detail is designed around you.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>This is a learning project</h2>
  <p>Built with plain PHP + SQLite by a student following the freeCodeCamp PHP course.
     Every page is hand-crafted to feel like the real thing.</p>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/products">Start browsing</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
