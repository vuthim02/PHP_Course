<?php
// views/help.php — the main help hub (broader than customer-service topics).
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Help &amp; Customer Service</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>We're here to help</h2>
    <p>Pick a topic below or jump straight to tracking an order, starting a return,
       or reading about shipping.</p>
    <a class="amz-btn amz-btn-white" href="/customer-service">More help topics</a>
  </div>
  <img class="amz-banner-img" src="/img/products/mug.jpg" alt="Help" loading="lazy">
</div>

<div class="amz-account-grid">
  <a class="amz-account-card" href="/account/orders">
    <div class="amz-account-icon">&#128230;</div>
    <h3>Your Orders</h3>
    <p>Track packages, view orders, and buy again</p>
  </a>
  <a class="amz-account-card" href="/returns">
    <div class="amz-account-icon">&#128257;</div>
    <h3>Returns &amp; Replacements</h3>
    <p>Return an item or check a refund status</p>
  </a>
  <a class="amz-account-card" href="/shipping-policies">
    <div class="amz-account-icon">&#128666;</div>
    <h3>Shipping &amp; delivery</h3>
    <p>Rates, delivery times, and tracking</p>
  </a>
  <a class="amz-account-card" href="/gift-cards">
    <div class="amz-account-icon">&#127873;</div>
    <h3>Gift Cards</h3>
    <p>Buy, redeem, and check balances</p>
  </a>
  <a class="amz-account-card" href="/account">
    <div class="amz-account-icon">&#128100;</div>
    <h3>Your Account</h3>
    <p>Manage your profile and sign-in</p>
  </a>
  <a class="amz-account-card" href="/sell">
    <div class="amz-account-icon">&#128176;</div>
    <h3>Selling</h3>
    <p>Help for sellers on amazone</p>
  </a>
</div>

<div class="amz-card amz-help-faq">
  <h2>Popular questions</h2>
  <details>
    <summary>How do I check out as a guest?</summary>
    <p>Just add items to your cart and go through checkout &mdash; you don't need an
       account, though signing in makes tracking orders easier.</p>
  </details>
  <details>
    <summary>What payment methods do you accept?</summary>
    <p>Card payments are handled securely by Stripe in test mode. In a real store you'd
       also see gift cards, bank transfers, and more.</p>
  </details>
  <details>
    <summary>Is my card information safe?</summary>
    <p>Yes. Card details are entered into Stripe's secure payment form and never touch
       this server.</p>
  </details>
  <details>
    <summary>How do I contact support?</summary>
    <p>This demo doesn't have live chat, but your most common questions are answered on
       the <a href="/customer-service">Customer Service</a> page.</p>
  </details>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
