<?php
// admin/reviews.php — review moderation list.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<h1 class="amz-page-title">Reviews (<?php echo count($reviews); ?>)</h1>

<div class="amz-admin-card">
  <table class="amz-admin-table">
    <thead>
      <tr><th>#</th><th>Product</th><th>Rating</th><th>By</th><th>Review</th><th>Date</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (count($reviews) === 0) { ?>
        <tr><td colspan="7" class="amz-sum-note">No reviews yet.</td></tr>
      <?php } ?>
      <?php foreach ($reviews as $r) { ?>
        <tr>
          <td><?php echo (int) $r["id"]; ?></td>
          <td><a href="/product/<?php echo e($r["product_slug"]); ?>"><?php echo e($r["product_name"]); ?></a></td>
          <td><?php echo stars((float) $r["rating"]); ?></td>
          <td><?php echo e($r["user_name"]); ?><br><span class="amz-hint"><?php echo e($r["user_email"]); ?></span></td>
          <td><?php echo nl2br(e($r["comment"] ?? "")); ?></td>
          <td><?php echo e(date("M j, Y", strtotime($r["created_at"]))); ?></td>
          <td>
            <form action="/admin/reviews/<?php echo (int) $r["id"]; ?>/delete" method="post"
                  onsubmit="return confirm('Remove this review?');">
              <?php echo csrf_field(); ?>
              <button class="amz-btn amz-btn-danger amz-btn-sm" type="submit">Remove</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
