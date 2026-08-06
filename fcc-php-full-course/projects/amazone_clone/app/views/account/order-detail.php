<?php
// views/account/order-detail.php — a single past order (owner-only).
include __DIR__ . "/../layout/header.php";
?>
<div class="amz-order-confirm">
  <h1>Order #<?php echo (int) $order["id"]; ?></h1>
  <p>Placed on <?php echo e(date("F j, Y g:i A", strtotime($order["created_at"]))); ?></p>
  <span class="amz-status amz-status-<?php echo e($order["status"]); ?>"><?php echo strtoupper(e($order["status"])); ?></span>
</div>

<div class="amz-checkout-layout">
  <div class="amz-card">
    <h2 class="amz-step">Items in this order</h2>
    <?php foreach ($items as $item) { ?>
      <div class="amz-cart-item">
        <a class="amz-cart-item-img" href="/product/<?php echo e($item["slug"]); ?>">
          <?php if (!empty($item["image_url"])) { ?>
            <img src="<?php echo e($item["image_url"]); ?>" alt="<?php echo e($item["name"]); ?>">
          <?php } else { ?>
            <span><?php echo e(strtoupper(substr($item["name"], 0, 1))); ?></span>
          <?php } ?>
        </a>
        <div class="amz-cart-item-info">
          <a class="amz-cart-item-title" href="/product/<?php echo e($item["slug"]); ?>"><?php echo e($item["name"]); ?></a>
          <div class="amz-sum-note">Qty: <?php echo (int) $item["quantity"]; ?></div>
        </div>
        <div class="amz-cart-item-price"><?php echo money($item["price_at_purchase"] * $item["quantity"]); ?></div>
      </div>
    <?php } ?>
  </div>

  <aside class="amz-order-summary">
    <h2>Summary</h2>
    <div class="amz-sum-row"><span>Items</span><span><?php echo money($order["total"]); ?></span></div>
    <div class="amz-sum-row amz-sum-total"><span>Total</span><span><?php echo money($order["total"]); ?></span></div>
    <h3>Delivering to</h3>
    <p class="amz-sum-note">
      <?php echo e($order["shipping_address"]); ?><br>
      <?php echo e($order["city"]); ?>, <?php echo e($order["zip"]); ?>
    </p>
    <a class="amz-btn amz-btn-yellow" href="/account/orders">Back to Your Orders</a>
  </aside>
</div>
<?php include __DIR__ . "/../layout/footer.php"; ?>
