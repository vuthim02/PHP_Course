<?php
// views/deals.php — Today's Deals with Deal of the Day + category filter.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Today's Deals</h1>

<div class="amz-chips">
  <a class="amz-chip <?php echo $categoryId === 0 ? "active" : ""; ?>" href="/deals">All deals</a>
  <?php foreach ($categories as $cat) { ?>
    <a class="amz-chip <?php echo $categoryId === (int) $cat["id"] ? "active" : ""; ?>"
       href="/deals?category=<?php echo (int) $cat["id"]; ?>"><?php echo e($cat["name"]); ?></a>
  <?php } ?>
</div>

<?php if ($dealOfDay !== null && $categoryId === 0) {
  $dealSave = (float) $dealOfDay["price"] - (float) $dealOfDay["deal_price"];
  $dealPct  = (int) round($dealSave / (float) $dealOfDay["price"] * 100);
?>
  <section class="amz-deal-day">
    <a class="amz-deal-day-img" href="/product/<?php echo e($dealOfDay["slug"]); ?>">
      <?php if (!empty($dealOfDay["image_url"])) { ?>
        <img src="<?php echo e($dealOfDay["image_url"]); ?>" alt="<?php echo e($dealOfDay["name"]); ?>">
      <?php } else { ?>
        <span><?php echo e(strtoupper(substr($dealOfDay["name"], 0, 1))); ?></span>
      <?php } ?>
    </a>
    <div class="amz-deal-day-info">
      <span class="amz-hero-tag">Deal of the Day</span>
      <a class="amz-deal-day-title" href="/product/<?php echo e($dealOfDay["slug"]); ?>"><?php echo e($dealOfDay["name"]); ?></a>
      <div class="amz-deal-day-price">
        <span class="amz-original"><?php echo money($dealOfDay["price"]); ?></span>
        <?php echo money($dealOfDay["deal_price"]); ?>
      </div>
      <p class="amz-deal-day-save">You save <?php echo money($dealSave); ?> (<?php echo $dealPct; ?>%).</p>
      <span class="amz-deal-timer" data-deal-timer>Ends today in <strong>--:--:--</strong></span>
      <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/product/<?php echo e($dealOfDay["slug"]); ?>">Shop this deal</a>
    </div>
  </section>
<?php } ?>

<section class="amz-shelf">
  <h2><?php echo $categoryId > 0 ? e($categoryName) : "Limited time deals"; ?></h2>
  <?php if (empty($deals)) { ?>
    <div class="amz-card amz-order-empty">
      <h2>No deals in this department right now.</h2>
      <p>New deals are added all the time &mdash; check back soon.</p>
      <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/deals">View all deals</a>
    </div>
  <?php } else { ?>
    <div class="amz-product-grid">
      <?php foreach ($deals as $p) {
          include __DIR__ . "/partials/product-card.php";
      } ?>
    </div>
  <?php } ?>
</section>
<?php include __DIR__ . "/layout/footer.php"; ?>
