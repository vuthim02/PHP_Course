<?php
// admin/returns.php — list return requests with a status filter.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<h1 class="amz-page-title">Returns</h1>

<div class="amz-admin-filter">
  <a href="/admin/returns"<?php echo $status === "" ? " class='active'" : ""; ?>>All</a>
  <?php foreach ($statuses as $s) { ?>
    <a href="/admin/returns?status=<?php echo e($s); ?>"<?php echo $status === $s ? " class='active'" : ""; ?>><?php echo e(ucfirst($s)); ?></a>
  <?php } ?>
</div>

<div class="amz-admin-card">
  <table class="amz-admin-table">
    <thead>
      <tr><th>#</th><th>Order</th><th>Date</th><th>Customer</th><th>Reason</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (count($requests) === 0) { ?>
        <tr><td colspan="7" class="amz-sum-note">No return requests<?php echo $status !== "" ? " with that status" : ""; ?>.</td></tr>
      <?php } ?>
      <?php foreach ($requests as $r) { ?>
        <tr>
          <td><?php echo (int) $r["id"]; ?></td>
          <td>#<?php echo (int) $r["order_id"]; ?></td>
          <td><?php echo e(date("M j, Y", strtotime($r["created_at"]))); ?></td>
          <td><?php echo e($r["user_name"] ?? "Guest"); ?><br>
              <span class="amz-hint"><?php echo e($r["user_email"] ?? ""); ?></span></td>
          <td><?php echo e(mb_strimwidth($r["reason"], 0, 60, "…")); ?></td>
          <td><span class="amz-status amz-status-<?php echo e($r["status"]); ?>"><?php echo e($r["status"]); ?></span></td>
          <td><a href="/admin/returns/<?php echo (int) $r["id"]; ?>">Manage</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
