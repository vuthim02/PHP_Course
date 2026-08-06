<?php
// views/sell.php — "Sell on amazone" pitch page.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Sell on amazone</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Turn what you make into money</h2>
    <p>Start selling today &mdash; reach millions of customers without building your
       own store. Simple fees, easy tools, worldwide shipping.</p>
    <a class="amz-btn amz-btn-white" href="/register">Start selling</a>
  </div>
  <img class="amz-banner-img" src="/img/products/keyboard.jpg" alt="Sell on amazone" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128200;</div>
    <h3>Millions of customers</h3>
    <p>Your products appear next to the best-known brands.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128279;</div>
    <h3>Easy to list</h3>
    <p>List a product in minutes with photos and a price.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128666;</div>
    <h3>We handle delivery</h3>
    <p>amazone can pick, pack, and ship your orders for you.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128176;</div>
    <h3>Pay as you sell</h3>
    <p>Pay only a referral fee when an item sells &mdash; no monthly fee to start.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>How it works</h2>
  <ol class="amz-steps">
    <li><strong>Create your account</strong> &mdash; sign in and set up your seller profile.</li>
    <li><strong>List your products</strong> &mdash; add photos, descriptions, and prices.</li>
    <li><strong>Get paid</strong> &mdash; we handle orders, delivery, and pay you out.</li>
  </ol>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/register">Create your seller account</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
