<?php
// admin/product_form.php — shared "add / edit product" form.
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";

$isEdit = $product !== null;
$val = fn(string $key, string $default = "") =>
    (string) ($old[$key] ?? $product[$key] ?? $default);
?>
<h1 class="amz-page-title"><?php echo $isEdit ? "Edit Product" : "Add Product"; ?></h1>

<form class="amz-admin-form" action="<?php echo $isEdit ? "/admin/products/" . (int) $product["id"] : "/admin/products"; ?>" method="post" enctype="multipart/form-data">
  <?php echo csrf_field(); ?>

  <div class="amz-field">
    <label for="p-name">Product name *</label>
    <input id="p-name" type="text" name="name" required value="<?php echo e($val("name")); ?>">
  </div>

  <div class="amz-field">
    <label for="p-slug">URL slug <span class="amz-hint">(leave empty to auto-generate from the name)</span></label>
    <input id="p-slug" type="text" name="slug" value="<?php echo e($val("slug")); ?>" placeholder="wireless-bluetooth-headphones">
  </div>

  <div class="amz-field">
    <label for="p-category">Category *</label>
    <select id="p-category" name="category_id" required>
      <option value="">— choose —</option>
      <?php foreach ($categories as $c) { ?>
        <option value="<?php echo (int) $c["id"]; ?>"<?php echo (int) $val("category_id", "0") === (int) $c["id"] ? " selected" : ""; ?>>
          <?php echo e($c["name"]); ?>
        </option>
      <?php } ?>
    </select>
  </div>

  <div class="amz-admin-grid2">
    <div class="amz-field">
      <label for="p-price">Price (<?php echo e(config("site.currency")); ?>) *</label>
      <input id="p-price" type="number" name="price" step="0.01" min="0" required value="<?php echo e($val("price")); ?>">
    </div>
    <div class="amz-field">
      <label for="p-deal">Deal price (<?php echo e(config("site.currency")); ?>)</label>
      <input id="p-deal" type="number" name="deal_price" step="0.01" min="0" value="<?php echo e($val("deal_price")); ?>" placeholder="optional, lower than price">
    </div>
  </div>

  <div class="amz-field">
    <label for="p-stock">Stock *</label>
    <input id="p-stock" type="number" name="stock" min="0" step="1" required value="<?php echo e($val("stock", "0")); ?>">
  </div>

  <div class="amz-field">
    <label for="p-desc">Description</label>
    <textarea id="p-desc" name="description" rows="5"><?php echo e($val("description")); ?></textarea>
  </div>

  <div class="amz-field">
    <label for="p-image">Product image</label>
    <?php if ($isEdit && !empty($product["image_url"])) { ?>
      <div class="amz-admin-current-img">
        <img src="<?php echo e($product["image_url"]); ?>" alt="">
        <label><input type="checkbox" name="remove_image" value="1"> Remove this image</label>
      </div>
    <?php } ?>
    <input id="p-image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
    <span class="amz-hint">JPG, PNG, WEBP or GIF, up to 2 MB.</span>
  </div>

  <div class="amz-admin-form-actions">
    <button class="amz-btn amz-btn-yellow amz-btn-sm" type="submit"><?php echo $isEdit ? "Save changes" : "Create product"; ?></button>
    <a href="/admin/products">Cancel</a>
  </div>
</form>

<?php include __DIR__ . "/../layout/footer.php"; ?>
