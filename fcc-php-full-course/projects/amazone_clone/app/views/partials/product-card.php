<?php
// partials/product-card.php — one product in a grid. Expects $p from a loop.
$isChoice  = (float) $p["rating_avg"] >= 4.6 && (int) $p["rating_count"] >= 100;
$hasDeal   = !empty($p["deal_price"]) && (float) $p["deal_price"] < (float) $p["price"];
$showPrice = $hasDeal ? (float) $p["deal_price"] : (float) $p["price"];
$original  = $hasDeal ? (float) $p["price"] : null;
$percentOff = $hasDeal ? (int) round((1 - (float) $p["deal_price"] / (float) $p["price"]) * 100) : 0;
$freeShip  = $showPrice >= 25.0;
?>
<article class="amz-card">
  <a href="/product/<?php echo e($p["slug"]); ?>" class="amz-card-link">
    <div class="amz-card-img">
      <?php if (!empty($p["image_url"])) { ?>
        <img src="<?php echo e($p["image_url"]); ?>" alt="<?php echo e($p["name"]); ?>" loading="lazy">
      <?php } else { ?>
        <span class="amz-card-img-ph"><?php echo e(strtoupper(substr($p["name"], 0, 1))); ?></span>
      <?php } ?>
      <?php if ($hasDeal) { ?><span class="amz-badge-deal">-<?php echo $percentOff; ?>%</span><?php } ?>
    </div>

    <h3 class="amz-card-title"><?php echo e($p["name"]); ?></h3>

    <?php if ($isChoice) { ?><span class="amz-choice">Amazon's Choice</span><?php } ?>

    <div class="amz-card-rating">
      <?php echo stars((float) $p["rating_avg"]); ?>
      <span class="amz-card-count"><?php echo (int) $p["rating_count"]; ?></span>
    </div>

    <div class="amz-price-line">
      <?php if ($original !== null) { ?>
        <span class="amz-original"><?php echo money($original); ?></span>
      <?php } ?>
      <?php echo money($showPrice); ?>
    </div>

    <?php if ($freeShip) { ?><div class="amz-free-ship">FREE delivery</div><?php } ?>
  </a>
</article>
