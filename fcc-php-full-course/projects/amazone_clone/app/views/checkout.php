<?php
// checkout.php — step 1: shipping address + order summary.
include __DIR__ . "/layout/header.php";
?>

<h1 class="amz-page-title">Checkout</h1>

<?php if (!empty($_SESSION["flash"])) { ?>
  <div class="amz-alert"><?php echo e($_SESSION["flash"]); ?></div>
  <?php unset($_SESSION["flash"]); ?>
<?php } ?>

<div class="amz-checkout-layout">
  <div class="amz-card">
    <h2 class="amz-step">1. Shipping address</h2>
    <form action="/checkout" method="post">
      <?php echo csrf_field(); ?>
      <label class="amz-field">Street address
        <input type="text" name="shipping_address" required value="<?php echo e($_SESSION["address"]["shipping_address"] ?? $profile["shipping_address"] ?? ""); ?>">
      </label>
      <label class="amz-field">City
        <input type="text" name="city" required value="<?php echo e($_SESSION["address"]["city"] ?? $profile["city"] ?? ""); ?>">
      </label>
      <label class="amz-field">ZIP code
        <input type="text" name="zip" required value="<?php echo e($_SESSION["address"]["zip"] ?? $profile["zip"] ?? ""); ?>">
      </label>
      <button class="amz-btn amz-btn-yellow" type="submit">Continue to payment</button>
    </form>
  </div>

  <aside class="amz-order-summary">
    <h2>Order summary</h2>
    <div class="amz-sum-row"><span>Items</span><span><?php echo money($summary["subtotal"]); ?></span></div>
    <div class="amz-sum-row"><span>Shipping</span>
      <span><?php echo $summary["shipping"] > 0 ? money($summary["shipping"]) : "FREE"; ?></span></div>
    <div class="amz-sum-row"><span>Tax</span><span><?php echo money($summary["tax"]); ?></span></div>
    <div class="amz-sum-row amz-sum-total"><span>Order total</span><span><?php echo money($summary["total"]); ?></span></div>
    <p class="amz-sum-note">Payment is handled by Stripe in <strong>test mode</strong> —
       use card <code>4242 4242 4242 4242</code>.</p>
  </aside>
</div>

<?php include __DIR__ . "/layout/footer.php"; ?>
