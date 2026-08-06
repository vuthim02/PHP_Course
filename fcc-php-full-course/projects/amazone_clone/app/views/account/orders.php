<?php
// views/account/orders.php — order history for the signed-in user.
include __DIR__ . "/../layout/header.php";
?>
<h1 class="amz-page-title">Your Orders</h1>

<?php if (empty($orders)) { ?>
  <div class="amz-card amz-order-empty">
    <h2>You have no orders yet.</h2>
    <p>When you place an order it will show up here so you can track it.</p>
    <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/products">Start shopping</a>
  </div>
<?php } else { ?>
  <?php foreach ($orders as $o) { ?>
    <div class="amz-card amz-order-row">
      <div class="amz-order-meta">
        <span class="amz-sum-note">Order placed</span>
        <strong><?php echo e(date("M j, Y", strtotime($o["created_at"]))); ?></strong>
        <span class="amz-sum-note">Total</span>
        <strong><?php echo money($o["total"]); ?></strong>
        <span class="amz-sum-note">Order #</span>
        <strong><?php echo (int) $o["id"]; ?></strong>
      </div>
      <span class="amz-status amz-status-<?php echo e($o["status"]); ?>"><?php echo strtoupper(e($o["status"])); ?></span>
      <a class="amz-btn amz-btn-white amz-btn-inline" href="/account/orders/<?php echo (int) $o["id"]; ?>">View order</a>
    </div>
  <?php } ?>
<?php } ?>
<?php include __DIR__ . "/../layout/footer.php"; ?>
