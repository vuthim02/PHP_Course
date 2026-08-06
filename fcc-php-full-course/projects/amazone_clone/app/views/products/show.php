<?php
// products/show.php — Amazon-style product page with a buy box.
$pageTitle = $product["name"];
$isChoice  = (float) $product["rating_avg"] >= 4.6 && (int) $product["rating_count"] >= 100;
$hasDeal   = !empty($product["deal_price"]) && (float) $product["deal_price"] < (float) $product["price"];
$showPrice = $hasDeal ? (float) $product["deal_price"] : (float) $product["price"];
$percentOff = $hasDeal ? (int) round((1 - (float) $product["deal_price"] / (float) $product["price"]) * 100) : 0;
include __DIR__ . "/../layout/header.php";
?>

<div class="amz-pd">
  <div class="amz-pd-left">
    <div class="amz-pd-img">
      <?php if (!empty($product["image_url"])) { ?>
        <img src="<?php echo e($product["image_url"]); ?>" alt="<?php echo e($product["name"]); ?>">
      <?php } else { ?>
        <span><?php echo e(strtoupper(substr($product["name"], 0, 1))); ?></span>
      <?php } ?>
    </div>
    <p class="amz-pd-hover-hint">Hover to zoom</p>
  </div>

  <div class="amz-pd-info">
    <p class="amz-pd-brand">Visit the amazone Store</p>
    <h1><?php echo e($product["name"]); ?></h1>
    <div class="amz-pd-rating">
      <?php echo stars((float) $product["rating_avg"]); ?>
      <span class="amz-card-count"><?php echo (int) $product["rating_count"]; ?> ratings</span>
    </div>
    <div class="amz-pd-price-row">
      <?php if ($hasDeal) { ?>
        <span class="amz-original amz-pd-original"><?php echo money($product["price"]); ?></span>
        <span class="amz-badge-deal amz-pd-deal">-<?php echo $percentOff; ?>%</span>
      <?php } ?>
      <?php echo money($showPrice); ?>
      <?php if ($isChoice) { ?><span class="amz-choice">Amazon's Choice</span><?php } ?>
    </div>
    <p class="amz-pd-desc"><?php echo nl2br(e($product["description"])); ?></p>
    <p class="amz-pd-meta">Category: <?php echo e($product["category"]); ?></p>
  </div>

  <aside class="amz-buybox">
    <div class="amz-buybox-price">
      <?php if ($hasDeal) { ?>
        <span class="amz-original"><?php echo money($product["price"]); ?></span>
      <?php } ?>
      <?php echo money($showPrice); ?>
    </div>
    <p class="amz-stock <?php echo $product["stock"] > 0 ? "in" : "out"; ?>">
      <?php echo $product["stock"] > 0 ? "In Stock" : "Out of stock"; ?>
    </p>
    <p class="amz-buybox-ship">FREE delivery on orders over <?php echo money(25.0); ?>.
       Arrives by <strong>Friday, <?php echo date("F j"); ?></strong>.</p>

    <div class="amz-qty">
      <span class="amz-qty-label">Qty:</span>
      <button type="button" id="qty-minus" aria-label="Decrease quantity">&minus;</button>
      <span id="qty-val" class="amz-qty-val">1</span>
      <button type="button" id="qty-plus" aria-label="Increase quantity">+</button>
    </div>

    <button class="amz-btn amz-btn-yellow" type="button" data-add-to-cart="<?php echo (int) $product["id"]; ?>">
      Add to Cart
    </button>
    <button class="amz-btn amz-btn-orange" type="button" data-buy-now>Buy Now</button>

    <p class="amz-buybox-secure">&#128274; Secure transaction</p>
  </aside>
</div>

<section class="amz-about">
  <h2>About this item</h2>
  <ul>
    <li><?php echo e($product["description"]); ?></li>
    <li>Made to last with quality materials.</li>
    <li>Backed by the amazone 30-day return policy.</li>
    <li>From the store you can trust.</li>
  </ul>
</section>

<?php
$dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
foreach ($reviews as $r) {
    $dist[(int) $r["rating"]]++;
}
$reviewTotal = max(1, count($reviews));
?>
<section class="amz-reviews" id="reviews">
  <h2>Customer reviews</h2>

  <div class="amz-reviews-summary">
    <div class="amz-reviews-avg">
      <span class="amz-reviews-num"><?php echo e(number_format((float) $product["rating_avg"], 1)); ?></span>
      <span class="amz-reviews-stars"><?php echo stars((float) $product["rating_avg"]); ?></span>
      <span class="amz-card-count"><?php echo (int) $product["rating_count"]; ?> ratings</span>
    </div>
    <?php if (count($reviews) > 0) { ?>
      <div class="amz-reviews-bars">
        <?php for ($s = 5; $s >= 1; $s--) { ?>
          <div class="amz-reviews-bar">
            <span class="amz-reviews-bar-label"><?php echo $s; ?> star</span>
            <div class="amz-reviews-bar-track">
              <div class="amz-reviews-bar-fill" style="width: <?php echo (int) round($dist[$s] / $reviewTotal * 100); ?>%"></div>
            </div>
            <span class="amz-reviews-bar-count"><?php echo $dist[$s]; ?></span>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>

  <?php if ($myReview !== null) { ?>
    <div class="amz-review-mine">
      <h3>Your review</h3>
      <article class="amz-review">
        <div class="amz-review-head">
          <?php echo stars((float) $myReview["rating"]); ?>
          <span class="amz-sum-note">You &middot; <?php echo e(date("M j, Y", strtotime($myReview["created_at"]))); ?></span>
        </div>
        <?php if (!empty($myReview["comment"])) { ?><p><?php echo nl2br(e($myReview["comment"])); ?></p><?php } ?>
        <form class="amz-review-delete" action="/reviews/delete" method="post">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="review_id" value="<?php echo (int) $myReview["id"]; ?>">
          <input type="hidden" name="product_id" value="<?php echo (int) $product["id"]; ?>">
          <button type="submit">Delete my review</button>
        </form>
      </article>
    </div>
  <?php } elseif (current_user() !== null) { ?>
    <form class="amz-review-form" action="/reviews" method="post">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="product_id" value="<?php echo (int) $product["id"]; ?>">
      <h3>Rate this product</h3>
      <div class="amz-star-picker">
        <input type="radio" id="review-star-5" name="rating" value="5"><label for="review-star-5" title="5 stars">&#9733;</label>
        <input type="radio" id="review-star-4" name="rating" value="4"><label for="review-star-4" title="4 stars">&#9733;</label>
        <input type="radio" id="review-star-3" name="rating" value="3"><label for="review-star-3" title="3 stars">&#9733;</label>
        <input type="radio" id="review-star-2" name="rating" value="2"><label for="review-star-2" title="2 stars">&#9733;</label>
        <input type="radio" id="review-star-1" name="rating" value="1"><label for="review-star-1" title="1 star">&#9733;</label>
      </div>
      <textarea name="comment" rows="4" maxlength="2000" placeholder="Tell others what you think about this product (optional)"></textarea>
      <button class="amz-btn amz-btn-yellow amz-btn-inline" type="submit">Submit review</button>
    </form>
  <?php } else { ?>
    <p class="amz-review-signin"><a href="/login">Sign in</a> to write a review.</p>
  <?php } ?>

  <?php if (count($reviews) > 0) { ?>
    <div class="amz-review-list">
      <?php foreach ($reviews as $r) { ?>
        <article class="amz-review">
          <div class="amz-review-head">
            <?php echo stars((float) $r["rating"]); ?>
            <strong><?php echo e($r["user_name"]); ?></strong>
            <span class="amz-sum-note"><?php echo e(date("M j, Y", strtotime($r["created_at"]))); ?></span>
          </div>
          <?php if (!empty($r["comment"])) { ?><p><?php echo nl2br(e($r["comment"])); ?></p><?php } ?>
        </article>
      <?php } ?>
    </div>
  <?php } ?>
</section>

<?php include __DIR__ . "/../layout/footer.php"; ?>
