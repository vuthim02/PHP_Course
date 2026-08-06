<?php
// views/customer-service.php — help hub.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Customer Service</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Hello. What can we help you with?</h2>
    <p>Find answers fast &mdash; track orders, start a return, or learn how shipping works.</p>
  </div>
  <img class="amz-banner-img" src="/img/products/headphones.jpg" alt="Customer service" loading="lazy">
</div>

<div class="amz-account-grid">
  <a class="amz-account-card" href="/account/orders">
    <div class="amz-account-icon">&#128230;</div>
    <h3>Track your order</h3>
    <p>See where your delivery is right now</p>
  </a>
  <a class="amz-account-card" href="/returns">
    <div class="amz-account-icon">&#128257;</div>
    <h3>Returns &amp; refunds</h3>
    <p>Return an item or check a refund status</p>
  </a>
  <a class="amz-account-card" href="/cart">
    <div class="amz-account-icon">&#128666;</div>
    <h3>Shipping &amp; delivery</h3>
    <p>FREE delivery on orders over <?php echo money(25.0); ?></p>
  </a>
  <a class="amz-account-card" href="/gift-cards">
    <div class="amz-account-icon">&#127873;</div>
    <h3>Gift cards</h3>
    <p>Buy, check a balance, or redeem one</p>
  </a>
  <a class="amz-account-card" href="/account">
    <div class="amz-account-icon">&#128274;</div>
    <h3>Account security</h3>
    <p>Manage your sign-in and profile</p>
  </a>
  <a class="amz-account-card" href="/products">
    <div class="amz-account-icon">&#128218;</div>
    <h3>Products</h3>
    <p>Browse everything we sell</p>
  </a>
</div>

<div class="amz-card amz-help-faq">
  <h2>Browse help topics</h2>
  <details>
    <summary>Where is my order?</summary>
    <p>Sign in and open <a href="/account/orders">Your Orders</a> to see the latest status of every order you've placed.</p>
  </details>
  <details>
    <summary>How do I return an item?</summary>
    <p>Go to the <a href="/returns">Returns &amp; Replacements</a> page, pick the order, choose the items, and submit a return request.</p>
  </details>
  <details>
    <summary>How long does delivery take?</summary>
    <p>Standard delivery is 2&ndash;5 business days. Orders over <?php echo money(25.0); ?> ship FREE.</p>
  </details>
  <details>
    <summary>When will my refund arrive?</summary>
    <p>Once the returned items are received, the refund goes back to your original payment method within 2&ndash;3 business days.</p>
  </details>
  <details>
    <summary>Can I shop without an account?</summary>
    <p>Yes. Add items to your cart and check out as a guest &mdash; though signing in makes it easy to track orders and returns.</p>
  </details>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
