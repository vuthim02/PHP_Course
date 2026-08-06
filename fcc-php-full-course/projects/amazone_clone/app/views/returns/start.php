<?php
// views/returns/start.php — choose an order, then the items to return.
include __DIR__ . "/../layout/header.php";
?>
<h1 class="amz-page-title">Start a return</h1>

<?php if (empty($orders)) { ?>
  <div class="amz-card amz-order-empty">
    <h2>You don't have any orders yet.</h2>
    <p>You can only return items from orders you've placed.</p>
    <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/products">Start shopping</a>
  </div>
<?php } else { ?>
  <div class="amz-checkout-layout">
    <div class="amz-card">
      <h2 class="amz-step">1. Choose an order</h2>
      <form action="/returns/start" method="get">
        <label class="amz-field">Order
          <select name="order" class="amz-select">
            <?php foreach ($orders as $o) { ?>
              <option value="<?php echo (int) $o["id"]; ?>"
                <?php echo (int) $o["id"] === $orderId ? "selected" : ""; ?>>
                Order #<?php echo (int) $o["id"]; ?> &mdash;
                <?php echo e(date("M j, Y", strtotime($o["created_at"]))); ?> &mdash;
                <?php echo money($o["total"]); ?>
              </option>
            <?php } ?>
          </select>
        </label>
        <button class="amz-btn amz-btn-white amz-btn-inline" type="submit">Show items</button>
      </form>

      <?php if ($selected !== null) { ?>
        <h2 class="amz-step">2. Pick the items to return</h2>
        <form action="/returns/submit" method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="order_id" value="<?php echo (int) $selected["id"]; ?>">
          <?php foreach ($items as $item) { ?>
            <div class="amz-return-item">
              <input class="amz-return-check" type="checkbox" name="items[]"
                     value="<?php echo (int) $item["item_id"]; ?>">
              <span class="amz-return-thumb">
                <?php if (!empty($item["image_url"])) { ?>
                  <img src="<?php echo e($item["image_url"]); ?>" alt="<?php echo e($item["name"]); ?>">
                <?php } else { ?>
                  <?php echo e(strtoupper(substr($item["name"], 0, 1))); ?>
                <?php } ?>
              </span>
              <span class="amz-return-name">
                <strong><?php echo e($item["name"]); ?></strong>
                <small>Qty: <?php echo (int) $item["quantity"]; ?></small>
              </span>
              <span class="amz-cart-item-price"><?php echo money($item["price_at_purchase"] * $item["quantity"]); ?></span>
            </div>
          <?php } ?>
          <label class="amz-field">Reason for return
            <select name="reason" class="amz-select" required>
              <option value="">Select a reason</option>
              <option value="arrived_damaged">Arrived damaged</option>
              <option value="wrong_item">Wrong item was sent</option>
              <option value="did_not_match">Item didn't match the description</option>
              <option value="changed_mind">Changed my mind</option>
              <option value="no_longer_needed">No longer needed</option>
            </select>
          </label>
          <button class="amz-btn amz-btn-yellow" type="submit">Submit return request</button>
        </form>
      <?php } ?>
    </div>
  </div>
<?php } ?>
<?php include __DIR__ . "/../layout/footer.php"; ?>
