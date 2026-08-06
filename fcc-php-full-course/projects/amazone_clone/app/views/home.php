<?php
// home.php — promo hero + category cards + featured products grid.
$pageTitle = "Online Shopping for Electronics, Books & More";
include __DIR__ . "/layout/header.php";
?>

<section class="amz-hero">
  <div class="amz-hero-text">
    <span class="amz-hero-kicker">Deals &amp; more</span>
    <h1>Great products, delivered to your door</h1>
    <p>Save on electronics, books, kitchen gear and more &mdash; with FREE delivery
       on orders over <?php echo money(25.0); ?>.</p>
    <a class="amz-btn amz-btn-yellow" href="/deals">Shop deals</a>
  </div>
  <a class="amz-hero-deal" href="/deals">
    <?php if ($dealOfDay !== null) { ?>
      <?php if (!empty($dealOfDay["image_url"])) { ?>
        <img src="<?php echo e($dealOfDay["image_url"]); ?>" alt="<?php echo e($dealOfDay["name"]); ?>" loading="lazy">
      <?php } ?>
      <span class="amz-hero-tag">Deal of the Day</span>
      <strong class="amz-hero-price"><?php echo money($dealOfDay["deal_price"]); ?></strong>
      <span class="amz-hero-name"><?php echo e($dealOfDay["name"]); ?></span>
    <?php } ?>
  </a>
</section>

<?php if (!empty($categories)) { ?>
<section class="amz-category-grid">
  <?php foreach ($categories as $cat) { ?>
    <a class="amz-category-card" href="/products?category=<?php echo (int) $cat["id"]; ?>">
      <div class="amz-category-img">
        <?php if (!empty($cat["image_url"])) { ?>
          <img src="<?php echo e($cat["image_url"]); ?>" alt="<?php echo e($cat["name"]); ?>" loading="lazy">
        <?php } ?>
      </div>
      <span class="amz-category-title"><?php echo e($cat["name"]); ?></span>
      <span class="amz-category-link">Shop now</span>
    </a>
  <?php } ?>
</section>
<?php } ?>

<section class="amz-shelf">
  <h2>Featured Products <a class="amz-see-all" href="/products">See all</a></h2>
  <div class="amz-product-grid">
    <?php foreach ($products as $p) {
        include __DIR__ . "/partials/product-card.php";
    } ?>
  </div>
</section>

<section class="amz-banner">
  <div class="amz-banner-text">
    <h2>Popular in Books</h2>
    <p>Best-sellers loved by readers everywhere.</p>
    <a class="amz-btn amz-btn-white" href="/products?category=3">Explore Books</a>
  </div>
  <img class="amz-banner-img" src="/img/categories/cat-books.jpg" alt="Books" loading="lazy">
</section>

<?php include __DIR__ . "/layout/footer.php"; ?>
