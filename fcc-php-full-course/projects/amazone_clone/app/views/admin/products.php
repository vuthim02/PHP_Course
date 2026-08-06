<?php
// admin/products.php — manage all products.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<div class="amz-admin-head">
  <h1 class="amz-page-title">Products (<?php echo count($products); ?>)</h1>
  <a class="amz-btn amz-btn-yellow amz-btn-sm" href="/admin/products/new">+ Add product</a>
</div>

<form class="amz-admin-search" action="/admin/products" method="get">
  <input type="text" name="q" placeholder="Search products by name" value="<?php echo e($_GET["q"] ?? ""); ?>">
  <button class="amz-btn amz-btn-sm amz-btn-orange" type="submit">Search</button>
  <?php if (trim((string) ($_GET["q"] ?? "")) !== "") { ?>
    <a href="/admin/products">Clear</a>
  <?php } ?>
</form>

<div class="amz-admin-card">
  <table class="amz-admin-table">
    <thead>
      <tr>
        <th></th>
        <th>Product</th>
        <th>Category</th>
        <th>Price</th>
        <th>Deal</th>
        <th>Stock</th>
        <th>Rating</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($products) === 0) { ?>
        <tr><td colspan="8" class="amz-sum-note">No products match.</td></tr>
      <?php } ?>
      <?php foreach ($products as $p) { ?>
        <tr>
          <td>
            <?php if (!empty($p["image_url"])) { ?>
              <img class="amz-admin-thumb" src="<?php echo e($p["image_url"]); ?>" alt="">
            <?php } ?>
          </td>
          <td><a href="/admin/products/<?php echo (int) $p["id"]; ?>/edit"><?php echo e($p["name"]); ?></a></td>
          <td><?php echo e($p["category"]); ?></td>
          <td><?php echo money($p["price"]); ?></td>
          <td><?php echo $p["deal_price"] !== null && (float) $p["deal_price"] < (float) $p["price"] ? money($p["deal_price"]) : "—"; ?></td>
          <td class="<?php echo (int) $p["stock"] <= 5 ? "amz-stock-warn" : ""; ?>"><?php echo (int) $p["stock"]; ?></td>
          <td><?php echo e(number_format((float) $p["rating_avg"], 1)); ?> (<?php echo (int) $p["rating_count"]; ?>)</td>
          <td class="amz-admin-actions">
            <a href="/admin/products/<?php echo (int) $p["id"]; ?>/edit">Edit</a>
            <form action="/admin/products/<?php echo (int) $p["id"]; ?>/duplicate" method="post">
              <?php echo csrf_field(); ?>
              <button type="submit" title="Duplicate this product">Duplicate</button>
            </form>
            <form action="/admin/products/<?php echo (int) $p["id"]; ?>/delete" method="post" onsubmit="return confirm('Delete this product?');">
              <?php echo csrf_field(); ?>
              <button type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . "/../layout/footer.php"; ?>
