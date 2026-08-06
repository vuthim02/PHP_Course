<?php
// cart.php — Amazon-style cart page (items left, subtotal box right).
include __DIR__ . "/layout/header.php";
?>

<h1 class="amz-page-title">Shopping Cart</h1>

<?php if (empty($items)) { ?>
  <div class="amz-card amz-empty-cart">
    <h2>Your amazone Cart is empty.</h2>
    <p><a href="/products">Shop today's deals</a></p>
  </div>
<?php } else { ?>
  <div class="amz-cart-layout">
    <div class="amz-cart-items">
      <?php foreach ($items as $item) { ?>
        <div class="amz-cart-item">
          <a class="amz-cart-item-img" href="/product/<?php echo e($item["slug"]); ?>">
            <?php if (!empty($item["image_url"])) { ?>
              <img src="<?php echo e($item["image_url"]); ?>" alt="<?php echo e($item["name"]); ?>">
            <?php } else { ?>
              <span><?php echo e(strtoupper(substr($item["name"], 0, 1))); ?></span>
            <?php } ?>
          </a>
          <div class="amz-cart-item-info">
            <a class="amz-cart-item-title" href="/product/<?php echo e($item["slug"]); ?>">
              <?php echo e($item["name"]); ?>
            </a>
            <div class="amz-cart-item-stock in">In Stock</div>
            <div class="amz-cart-item-actions">
              <form action="/cart/update" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int) $item["id"]; ?>">
                <label>Qty:
                  <select name="quantity" onchange="this.form.submit()">
                    <?php for ($i = 1; $i <= min(10, (int) $item["stock"]); $i++) { ?>
                      <option value="<?php echo $i; ?>" <?php echo $i === (int) $item["quantity"] ? "selected" : ""; ?>>
                        <?php echo $i; ?>
                      </option>
                    <?php } ?>
                  </select>
                </label>
              </form>
              <form action="/cart/remove" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo (int) $item["id"]; ?>">
                <button class="amz-link-btn" type="submit">Delete</button>
              </form>
            </div>
          </div>
          <div class="amz-cart-item-price"><?php echo money($item["price"] * $item["quantity"]); ?></div>
        </div>
      <?php } ?>
    </div>

    <aside class="amz-cart-subtotal">
      <div class="amz-cart-subtotal-price">
        Subtotal (<?php echo \App\Models\Cart::count(); ?> items): <?php echo money($summary["subtotal"]); ?>
      </div>
      <a class="amz-btn amz-btn-yellow" href="/checkout">Proceed to checkout</a>
      <a class="amz-btn amz-btn-white" href="/products">Continue shopping</a>
    </aside>
  </div>
<?php } ?>

<?php include __DIR__ . "/layout/footer.php"; ?>
