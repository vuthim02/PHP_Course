<?php
// views/affiliate.php — amazone Associates program pitch.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Become an Affiliate</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Earn money promoting amazone</h2>
    <p>Share products you love, earn referral fees when people buy through your links.
       Free to join &mdash; start earning from day one.</p>
    <a class="amz-btn amz-btn-white" href="/register">Join for free</a>
  </div>
  <img class="amz-banner-img" src="/img/products/phpbook.jpg" alt="Become an Affiliate" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128279;</div>
    <h3>Your links, your earnings</h3>
    <p>Every sale through your link earns a commission &mdash; up to 10%.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128200;</div>
    <h3>Real-time dashboard</h3>
    <p>Track clicks and earnings the moment they happen.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128176;</div>
    <h3>No fees to start</h3>
    <p>Join free. You only earn &mdash; never pay to participate.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>How it works</h2>
  <ol class="amz-steps">
    <li><strong>Join</strong> &mdash; create a free account.</li>
    <li><strong>Grab a link</strong> &mdash; pick any product from the store.</li>
    <li><strong>Share it</strong> &mdash; on a blog, social media, or to friends.</li>
    <li><strong>Get paid</strong> &mdash; earn a referral fee on every sale.</li>
  </ol>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/register">Become an affiliate</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
