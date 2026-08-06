<?php
// admin/return_detail.php — approve/deny/receive a return, see its items.
$request = $data["request"];
$items   = $data["items"];
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<div class="amz-admin-head">
  <h1 class="amz-page-title">Return #<?php echo (int) $request["id"]; ?></h1>
  <span class="amz-status amz-status-<?php echo e($request["status"]); ?>"><?php echo e($request["status"]); ?></span>
</div>

<div class="amz-admin-cols">
  <section class="amz-admin-card">
    <h2>Details</h2>
    <dl class="amz-admin-dl">
      <dt>Requested</dt><dd><?php echo e(date("M j, Y \a\t g:i A", strtotime($request["created_at"]))); ?></dd>
      <dt>Order</dt><dd><a href="/admin/orders/<?php echo (int) $request["order_id"]; ?>">#<?php echo (int) $request["order_id"]; ?></a></dd>
      <dt>Reason</dt><dd><?php echo nl2br(e($request["reason"])); ?></dd>
      <dt>Ship to</dt><dd><?php echo nl2br(e($request["shipping_address"])); ?><br><?php echo e($request["city"]); ?>, <?php echo e($request["zip"]); ?></dd>
    </dl>
  </section>

  <section class="amz-admin-card">
    <h2>Update status</h2>
    <form class="amz-admin-form" action="/admin/returns/<?php echo (int) $request["id"]; ?>/status" method="post">
      <?php echo csrf_field(); ?>
      <div class="amz-field">
        <label for="r-status">Return status</label>
        <select id="r-status" name="status">
          <?php foreach ($statuses as $s) { ?>
            <option value="<?php echo e($s); ?>"<?php echo $request["status"] === $s ? " selected" : ""; ?>><?php echo e(ucfirst($s)); ?></option>
          <?php } ?>
        </select>
      </div>
      <button class="amz-btn amz-btn-yellow amz-btn-sm" type="submit">Update status</button>
    </form>
    <p class="amz-hint amz-sum-note">
      Marking a return <strong>received</strong> automatically adds the returned quantities back to stock.
    </p>
  </section>
</div>

<section class="amz-admin-card">
  <h2>Items being returned (<?php echo count($items); ?>)</h2>
  <table class="amz-admin-table">
    <thead><tr><th></th><th>Product</th><th>Qty</th><th>Price paid</th><th>Refund value</th></tr></thead>
    <tbody>
      <?php foreach ($items as $i) { ?>
        <tr>
          <td>
            <?php if (!empty($i["image_url"])) { ?>
              <img class="amz-admin-thumb" src="<?php echo e($i["image_url"]); ?>" alt="">
            <?php } ?>
          </td>
          <td><a href="/product/<?php echo e($i["slug"]); ?>"><?php echo e($i["name"]); ?></a></td>
          <td><?php echo (int) $i["return_qty"]; ?></td>
          <td><?php echo money($i["price_at_purchase"]); ?></td>
          <td><?php echo money((float) $i["price_at_purchase"] * (int) $i["return_qty"]); ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</section>

<?php include __DIR__ . "/../layout/footer.php"; ?>
