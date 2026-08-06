<?php
// admin/categories.php — add + manage categories.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<div class="amz-admin-head">
  <h1 class="amz-page-title">Categories</h1>
</div>

<div class="amz-admin-cols">
  <section class="amz-admin-card">
    <h2>Add category</h2>
    <form class="amz-admin-form" action="/admin/categories" method="post">
      <?php echo csrf_field(); ?>
      <div class="amz-field">
        <label for="c-name">Category name *</label>
        <input id="c-name" type="text" name="name" required placeholder="e.g. Toys & Games">
        <span class="amz-hint">The URL slug is generated automatically from the name.</span>
      </div>
      <button class="amz-btn amz-btn-yellow amz-btn-sm" type="submit">Add category</button>
    </form>
  </section>

  <section class="amz-admin-card">
    <h2>All categories</h2>
    <table class="amz-admin-table">
      <thead><tr><th>Name</th><th>Products</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($categories as $c) { ?>
          <tr>
            <td><?php echo e($c["name"]); ?></td>
            <td><?php echo (int) $c["product_count"]; ?></td>
            <td class="amz-admin-actions">
              <form action="/admin/categories/<?php echo (int) $c["id"]; ?>/delete" method="post"
                    onsubmit="return confirm('Delete \'<?php echo e($c["name"]); ?>\' and ALL its products?');">
                <?php echo csrf_field(); ?>
                <button type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </section>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
