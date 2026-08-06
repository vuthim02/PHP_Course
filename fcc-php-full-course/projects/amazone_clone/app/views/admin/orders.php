<?php
// admin/orders.php — list orders with a status filter + search.
$qs = $q !== "" ? "&q=" . urlencode($q) : "";
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<h1 class="amz-page-title">Orders</h1>

<form class="amz-admin-search" action="/admin/orders" method="get">
  <input type="text" name="q" placeholder="Search by order #, customer name or email" value="<?php echo e($q); ?>">
  <?php if ($status !== "") { ?>
    <input type="hidden" name="status" value="<?php echo e($status); ?>">
  <?php } ?>
  <button class="amz-btn amz-btn-sm amz-btn-orange" type="submit">Search</button>
  <?php if ($q !== "" || $status !== "") { ?>
    <a href="/admin/orders">Clear</a>
  <?php } ?>
</form>

<div class="amz-admin-filter">
  <a href="/admin/orders<?php echo $q !== "" ? "?q=" . urlencode($q) : ""; ?>"<?php echo $status === "" ? " class='active'" : ""; ?>>All</a>
  <?php foreach ($statuses as $s) { ?>
    <a href="/admin/orders?status=<?php echo e($s); ?><?php echo $qs; ?>"<?php echo $status === $s ? " class='active'" : ""; ?>><?php echo e(ucfirst($s)); ?></a>
  <?php } ?>
</div>

<div class="amz-admin-card">
  <table class="amz-admin-table">
    <thead>
      <tr><th>#</th><th>Date</th><th>Customer</th><th>Ship to</th><th>Total</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (count($orders) === 0) { ?>
        <tr><td colspan="7" class="amz-sum-note">No orders<?php echo $status !== "" ? " with that status" : ""; ?>.</td></tr>
      <?php } ?>
      <?php foreach ($orders as $o) { ?>
        <tr>
          <td><?php echo (int) $o["id"]; ?></td>
          <td><?php echo e(date("M j, Y", strtotime($o["created_at"]))); ?></td>
          <td><?php echo e($o["user_name"] ?? "Guest"); ?><br>
              <span class="amz-hint"><?php echo e($o["user_email"] ?? ""); ?></span></td>
          <td><?php echo e($o["shipping_address"]); ?>, <?php echo e($o["city"]); ?> <?php echo e($o["zip"]); ?></td>
          <td><?php echo money($o["total"]); ?></td>
          <td><span class="amz-status amz-status-<?php echo e($o["status"]); ?>"><?php echo e($o["status"]); ?></span></td>
          <td><a href="/admin/orders/<?php echo (int) $o["id"]; ?>">Manage</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
