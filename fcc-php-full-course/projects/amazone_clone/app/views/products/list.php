<?php
// products/list.php — grid of products with category filter chips.
$pageTitle = $pageTitle ?? "All Products";
$categoryId = $categoryId ?? 0;
include __DIR__ . "/../layout/header.php";
?>

<h1 class="amz-page-title"><?php echo e($pageTitle); ?></h1>

<div class="amz-chips">
  <a class="amz-chip<?php echo $categoryId === 0 ? " active" : ""; ?>" href="/products">All</a>
  <?php foreach ($categories as $cat) { ?>
    <a class="amz-chip<?php echo $categoryId === (int) $cat["id"] ? " active" : ""; ?>"
       href="/products?category=<?php echo (int) $cat["id"]; ?>">
      <?php echo e($cat["name"]); ?>
    </a>
  <?php } ?>
</div>

<?php if (empty($products)) { ?>
  <p class="amz-empty">No products found.</p>
<?php } else { ?>
  <div class="amz-product-grid">
    <?php foreach ($products as $p) {
        include __DIR__ . "/../partials/product-card.php";
    } ?>
  </div>
<?php } ?>

<?php include __DIR__ . "/../layout/footer.php"; ?>
