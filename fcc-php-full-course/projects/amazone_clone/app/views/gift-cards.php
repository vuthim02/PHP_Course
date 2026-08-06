<?php
// views/gift-cards.php — amazone gift cards.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">amazone Gift Cards</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Never guess a gift again</h2>
    <p>From $25 to $200 &mdash; delivered by email, no expiration, and good on
       millions of items across amazone.</p>
    <a class="amz-btn amz-btn-white" href="#gift-cards">Shop gift cards</a>
  </div>
  <img class="amz-banner-img" src="/img/categories/cat-giftcards.svg" alt="Gift cards" loading="lazy">
</div>

<div class="amz-gift-grid" id="gift-cards">
  <?php foreach ($giftCards as $g) { ?>
    <div class="amz-card amz-gift-card">
      <a class="amz-gift-card-img" href="/product/<?php echo e($g["slug"]); ?>">
        <img src="<?php echo e($g["image_url"]); ?>" alt="<?php echo e($g["name"]); ?>">
      </a>
      <h3 class="amz-card-title"><?php echo e($g["name"]); ?></h3>
      <div class="amz-gift-card-value"><?php echo money($g["price"]); ?></div>
      <p class="amz-sum-note">Delivered by email within minutes.</p>
      <button class="amz-btn amz-btn-yellow" type="button" data-add-to-cart="<?php echo (int) $g["id"]; ?>">Add to Cart</button>
    </div>
  <?php } ?>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
