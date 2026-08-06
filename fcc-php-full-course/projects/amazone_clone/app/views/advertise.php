<?php
// views/advertise.php — Sponsored Products ad pitch.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Advertise Your Products</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Put your products in the spotlight</h2>
    <p>Sponsored Products appear right next to the search results shoppers are
       already browsing &mdash; and you only pay when someone clicks.</p>
    <a class="amz-btn amz-btn-white" href="/register">Start advertising</a>
  </div>
  <img class="amz-banner-img" src="/img/products/headphones.jpg" alt="Advertise your products" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128200;</div>
    <h3>Reach shoppers in-market</h3>
    <p>Show up exactly when customers are searching for what you sell.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128178;</div>
    <h3>Pay per click</h3>
    <p>Only pay when someone clicks your ad &mdash; impressions are free.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128295;</div>
    <h3>Full control</h3>
    <p>Set your budget, pick your keywords, and change anything anytime.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>Three simple steps</h2>
  <ol class="amz-steps">
    <li><strong>Create your ad account</strong> &mdash; sign in and connect your products.</li>
    <li><strong>Build a campaign</strong> &mdash; choose products, keywords, and a daily budget.</li>
    <li><strong>Watch it work</strong> &mdash; track clicks and sales in the dashboard.</li>
  </ol>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/register">Get started</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
