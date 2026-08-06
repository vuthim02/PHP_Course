<?php
// views/sustainability.php — green initiatives page.
include __DIR__ . "/layout/header.php";
?>
<h1 class="amz-page-title">Sustainability</h1>

<div class="amz-banner">
  <div class="amz-banner-text">
    <h2>Better products, better planet</h2>
    <p>We're working toward a climate-positive future &mdash; lighter packaging,
       smarter delivery routes, and products made to last.</p>
    <a class="amz-btn amz-btn-white" href="/products">Shop sustainably</a>
  </div>
  <img class="amz-banner-img" src="/img/products/tent.jpg" alt="Sustainability" loading="lazy">
</div>

<div class="amz-account-grid">
  <div class="amz-account-card">
    <div class="amz-account-icon">&#127793;</div>
    <h3>Climate Pledge</h3>
    <p>Net-zero carbon across the store by 2040.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128231;</div>
    <h3>Paperless by default</h3>
    <p>Digital receipts, digital gift cards, and less packaging overall.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128666;</div>
    <h3>Right-size packaging</h3>
    <p>Fewer, smaller boxes means fewer trucks and less waste.</p>
  </div>
  <div class="amz-account-card">
    <div class="amz-account-icon">&#128260;</div>
    <h3>Recyclable materials</h3>
    <p>Packaging designed to be recycled again and again.</p>
  </div>
</div>

<div class="amz-card amz-cta">
  <h2>Small steps add up</h2>
  <p>Choose FREE delivery on orders over <?php echo money(25.0); ?> to bundle your
     purchases into fewer shipments.</p>
  <a class="amz-btn amz-btn-yellow amz-btn-inline" href="/products">Shop now</a>
</div>
<?php include __DIR__ . "/layout/footer.php"; ?>
