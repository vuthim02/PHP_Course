<?php
// views/returns/show.php — a single return request (owner-only).
include __DIR__ . "/../layout/header.php";
?>
<div class="amz-order-confirm">
  <h1>Return #<?php echo (int) $request["id"]; ?></h1>
  <p>For order #<?php echo (int) $request["order_id"]; ?> &mdash;
     requested on <?php echo e(date("F j, Y", strtotime($request["created_at"]))); ?>.</p>
  <span class="amz-status amz-status-<?php echo e($request["status"]); ?>"><?php echo strtoupper(e($request["status"])); ?></span>
</div>

<div class="amz-checkout-layout">
  <div class="amz-card">
    <h2 class="amz-step">Items being returned</h2>
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
          <div class="amz-sum-note">Qty: <?php echo (int) $item["return_qty"]; ?></div>
        </div>
        <div class="amz-cart-item-price"><?php echo money($item["price_at_purchase"] * $item["return_qty"]); ?></div>
      </div>
    <?php } ?>
  </div>

  <aside class="amz-order-summary">
    <h2>Return summary</h2>
    <div class="amz-sum-row"><span>Reason</span><span><?php echo e(str_replace("_", " ", $request["reason"])); ?></span></div>
    <p class="amz-sum-note">Status updates appear here. Once the items are received,
       the refund goes back to your original payment method.</p>
    <a class="amz-btn amz-btn-yellow" href="/returns">Back to Returns</a>
  </aside>
</div>
<?php include __DIR__ . "/../layout/footer.php"; ?>
