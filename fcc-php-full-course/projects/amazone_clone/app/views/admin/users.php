<?php
// admin/users.php — list accounts + grant/revoke admin roles.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<h1 class="amz-page-title">Users (<?php echo count($users); ?>)</h1>

<div class="amz-admin-card">
  <table class="amz-admin-table">
    <thead>
      <tr><th>#</th><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th><th>Role</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u) { ?>
        <tr>
          <td><?php echo (int) $u["id"]; ?></td>
          <td><?php echo e($u["name"]); ?></td>
          <td><?php echo e($u["email"]); ?></td>
          <td><?php echo (int) $u["order_count"]; ?></td>
          <td><?php echo e(date("M j, Y", strtotime($u["created_at"]))); ?></td>
          <td><span class="amz-status amz-status-<?php echo (int) $u["is_admin"] ? "admin" : "customer"; ?>"><?php echo (int) $u["is_admin"] ? "Admin" : "Customer"; ?></span></td>
          <td>
            <?php if ((int) $u["id"] === (int) current_user()["id"]) { ?>
              <span class="amz-hint">You</span>
            <?php } else { ?>
              <form action="/admin/users/<?php echo (int) $u["id"]; ?>/toggle-admin" method="post"
                    onsubmit="return confirm('Change this user&rsquo;s admin role?');">
                <?php echo csrf_field(); ?>
                <button class="amz-btn amz-btn-sm <?php echo (int) $u["is_admin"] ? "amz-btn-orange" : "amz-btn-blue"; ?>" type="submit">
                  <?php echo (int) $u["is_admin"] ? "Remove admin" : "Make admin"; ?>
                </button>
              </form>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
