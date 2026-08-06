<?php
// admin/order_detail.php — inspect one order + change its status.
$order = $data["order"];
$items = $data["items"];
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<div class="amz-admin-head">
  <h1 class="amz-page-title">Order #<?php echo (int) $order["id"]; ?></h1>
  <span class="amz-status amz-status-<?php echo e($order["status"]); ?>"><?php echo e($order["status"]); ?></span>
</div>

<div class="amz-admin-cols">
  <section class="amz-admin-card">
    <h2>Details</h2>
    <dl class="amz-admin-dl">
      <dt>Placed</dt><dd><?php echo e(date("M j, Y \a\t g:i A", strtotime($order["created_at"]))); ?></dd>
      <dt>Customer</dt><dd><?php echo e($order["user_name"] ?? "Guest"); ?> &mdash; <?php echo e($order["user_email"] ?? "no account"); ?></dd>
      <dt>Ship to</dt><dd><?php echo nl2br(e($order["shipping_address"])); ?><br><?php echo e($order["city"]); ?>, <?php echo e($order["zip"]); ?></dd>
      <dt>Total</dt><dd><?php echo money($order["total"]); ?></dd>
    </dl>
  </section>

  <section class="amz-admin-card">
    <h2>Update status</h2>
    <form class="amz-admin-form" action="/admin/orders/<?php echo (int) $order["id"]; ?>/status" method="post">
      <?php echo csrf_field(); ?>
      <div class="amz-field">
        <label for="o-status">Order status</label>
        <select id="o-status" name="status">
          <?php foreach ($statuses as $s) { ?>
            <option value="<?php echo e($s); ?>"<?php echo $order["status"] === $s ? " selected" : ""; ?>><?php echo e(ucfirst($s)); ?></option>
          <?php } ?>
        </select>
      </div>
      <button class="amz-btn amz-btn-yellow amz-btn-sm" type="submit">Update status</button>
    </form>
    <?php if (in_array($order["status"], ["pending", "paid"], true)) { ?>
      <form class="amz-inline-form" action="/admin/orders/<?php echo (int) $order["id"]; ?>/cancel" method="post"
            onsubmit="return confirm('Cancel this order and return its items to stock?');">
        <?php echo csrf_field(); ?>
        <button class="amz-btn amz-btn-danger amz-btn-sm" type="submit">Cancel order &amp; restock</button>
      </form>
    <?php } else { ?>
      <p class="amz-hint amz-sum-note">This order can't be cancelled (<?php echo e($order["status"]); ?>).</p>
    <?php } ?>
  </section>
</div>

<section class="amz-admin-card">
  <h2>Items (<?php echo count($items); ?>)</h2>
  <table class="amz-admin-table">
    <thead><tr><th></th><th>Product</th><th>Qty</th><th>Price</th><th>Line total</th></tr></thead>
    <tbody>
      <?php foreach ($items as $i) { ?>
        <tr>
          <td>
            <?php if (!empty($i["image_url"])) { ?>
              <img class="amz-admin-thumb" src="<?php echo e($i["image_url"]); ?>" alt="">
            <?php } ?>
          </td>
          <td><a href="/product/<?php echo e($i["slug"]); ?>"><?php echo e($i["name"]); ?></a></td>
          <td><?php echo (int) $i["quantity"]; ?></td>
          <td><?php echo money($i["price_at_purchase"]); ?></td>
          <td><?php echo money((float) $i["price_at_purchase"] * (int) $i["quantity"]); ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . "/../layout/footer.php"; ?>
