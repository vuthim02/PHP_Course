<?php
// views/returns/landing.php — Returns & Replacements hub.
include __DIR__ . "/../layout/header.php";
?>
<h1 class="amz-page-title">Returns &amp; Replacements</h1>

<?php if (current_user() === null) { ?>
  <div class="amz-card amz-order-empty">
    <h2>Sign in to see your orders</h2>
    <p>You need to sign in to start a return or replacement.</p>
    <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/login">Sign in</a>
  </div>
<?php } else { ?>
  <div class="amz-card amz-return-hero">
    <div>
      <h2>Return or replace items</h2>
      <p>Start a return by choosing an order and selecting the items you want to send back.</p>
      <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/returns/start">Start a return</a>
      <a class="amz-btn amz-btn-white amz-btn-inline" href="/account/orders">View Your Orders</a>
    </div>
  </div>

  <?php if (empty($myReturns)) { ?>
    <div class="amz-card amz-order-empty">
      <h2>No returns yet</h2>
      <p>Anything you return will show up here with its status.</p>
    </div>
  <?php } else { ?>
    <h2 class="amz-returns-heading">Your recent returns</h2>
    <?php foreach ($myReturns as $r) { ?>
      <div class="amz-card amz-order-row">
        <div class="amz-order-meta">
          <span class="amz-sum-note">Return #</span>
          <strong><?php echo (int) $r["id"]; ?></strong>
          <span class="amz-sum-note">Order #</span>
          <strong><?php echo (int) $r["order_id"]; ?></strong>
          <span class="amz-sum-note">Requested</span>
          <strong><?php echo e(date("M j, Y", strtotime($r["created_at"]))); ?></strong>
        </div>
        <span class="amz-status amz-status-<?php echo e($r["status"]); ?>"><?php echo strtoupper(e($r["status"])); ?></span>
        <a class="amz-btn amz-btn-white amz-btn-inline" href="/returns/<?php echo (int) $r["id"]; ?>">View</a>
      </div>
    <?php } ?>
  <?php } ?>
<?php } ?>
<?php include __DIR__ . "/../layout/footer.php"; ?>
