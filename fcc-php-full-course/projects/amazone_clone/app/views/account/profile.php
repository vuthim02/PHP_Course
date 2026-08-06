<?php
// views/account/profile.php — account dashboard (Amazon-style tiles).
include __DIR__ . "/../layout/header.php";
?>
<h1 class="amz-page-title">Your Account</h1>

<div class="amz-account-grid">
  <a class="amz-account-card" href="/account/orders">
    <div class="amz-account-icon">&#128231;</div>
    <h3>Your Orders</h3>
    <p>Track, return, or buy things again</p>
  </a>
  <a class="amz-account-card" href="/cart">
    <div class="amz-account-icon">&#128722;</div>
    <h3>Your Cart</h3>
    <p>View items in your shopping cart</p>
  </a>
  <a class="amz-account-card" href="/returns">
    <div class="amz-account-icon">&#128259;</div>
    <h3>Returns &amp; Replacements</h3>
    <p>Return or replace items you bought</p>
  </a>
  <a class="amz-account-card" href="/account/orders">
    <div class="amz-account-icon">&#128230;</div>
    <h3>Your Addresses</h3>
    <p>Manage your shipping addresses</p>
  </a>
  <a class="amz-account-card" href="/account">
    <div class="amz-account-icon">&#128100;</div>
    <h3>Account Settings</h3>
    <p>Signed in as <?php echo e($profile["email"]); ?></p>
  </a>
</div>
<?php include __DIR__ . "/../layout/footer.php"; ?>
