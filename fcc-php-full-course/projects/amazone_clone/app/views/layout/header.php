<?php
// layout/header.php — Amazon-style header + nav shared by every page.
$pageTitle = $pageTitle ?? config("site.name", "amazone");
$categoriesForNav = \App\Models\Product::categories();
$cartCount = \App\Models\Cart::count();
$currentUser = current_user();

$navLinks = [
    "Today's Deals"     => "/deals",
    "Customer Service"  => "/customer-service",
    "Gift Cards"        => "/gift-cards",
    "Registry"          => "/registry",
    "Sell"              => "/sell",
];

$onAccount = str_starts_with($_SERVER["REQUEST_URI"] ?? "/", "/account");
$onReturns = str_starts_with($_SERVER["REQUEST_URI"] ?? "/", "/returns");
$onCart    = is_active("/cart");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo e($pageTitle); ?> &mdash; <?php echo e(config("site.name")); ?></title>
  <link rel="stylesheet" href="/css/amazon.css">
</head>
<body>
<header class="amz-header">
  <div class="amz-header-inner">
    <a class="amz-logo" href="/"><span>amazone</span></a>

    <div class="amz-deliver" id="deliver-box" data-maps-key="<?php echo e((string) config("google_maps.api_key", "")); ?>">
      <button class="amz-deliver-btn" id="deliver-toggle" type="button" aria-expanded="false">
        <span class="amz-deliver-top">Deliver to</span>
        <span class="amz-deliver-bottom">&#128205; <span id="deliver-name">Your Home</span></span>
      </button>
      <div class="amz-deliver-pop" id="deliver-pop" hidden>
        <h3 class="amz-deliver-title">Choose your location</h3>
        <p class="amz-deliver-note">Delivery options and delivery speeds may vary for different locations.</p>
        <div class="amz-deliver-search">
          <input id="deliver-input" type="text" placeholder="Enter a city, ZIP code or address" autocomplete="off">
          <button class="amz-deliver-apply" id="deliver-apply" type="button">Apply</button>
        </div>
        <button class="amz-deliver-locate" id="deliver-locate" type="button">&#128205; Use my current location</button>
        <div class="amz-deliver-map" id="deliver-map" hidden></div>
        <div class="amz-deliver-quick">
          <span>Popular cities</span>
          <button type="button" data-place="Seattle, WA 98101">Seattle</button>
          <button type="button" data-place="San Francisco, CA 94103">San Francisco</button>
          <button type="button" data-place="New York, NY 10001">New York</button>
          <button type="button" data-place="Austin, TX 78701">Austin</button>
          <button type="button" data-place="Chicago, IL 60601">Chicago</button>
        </div>
        <div class="amz-deliver-foot">
          <a id="deliver-open-map" href="https://www.google.com/maps" target="_blank" rel="noopener">Open in Google Maps &#8599;</a>
          <a href="/deliver-to">Manage locations</a>
          <?php if ($currentUser === null) { ?>
            <a href="/login">Sign in for your addresses</a>
          <?php } ?>
          <a href="/help">Need help?</a>
        </div>
      </div>
    </div>

    <form class="amz-search" action="/search" method="get" role="search">
      <input type="text" name="q" placeholder="Search amazone" value="<?php echo e($_GET["q"] ?? ""); ?>" aria-label="Search">
      <button type="submit" aria-label="Search">&#128269;</button>
    </form>

    <div class="amz-right">
      <?php if ($currentUser !== null) { ?>
        <div class="amz-account">
          <a class="amz-link<?php echo $onAccount ? " active" : ""; ?>" href="/account">
            <span>Hello, <?php echo e(explode(" ", $currentUser["name"])[0]); ?></span>
            <strong>Account &amp; Lists</strong>
          </a>
          <div class="amz-account-drop">
            <a href="/account">Your Account</a>
            <a href="/account/orders">Your Orders</a>
            <?php if ($currentUser["is_admin"]) { ?>
              <a href="/admin">Admin Panel</a>
            <?php } ?>
            <form action="/logout" method="post">
              <?php echo csrf_field(); ?>
              <button class="amz-link-btn" type="submit">Sign out</button>
            </form>
          </div>
        </div>
      <?php } else { ?>
        <a class="amz-link<?php echo $onAccount ? " active" : ""; ?>" href="/login">
          <span>Hello, sign in</span>
          <strong>Account &amp; Lists</strong>
        </a>
      <?php } ?>
      <a class="amz-link<?php echo $onReturns ? " active" : ""; ?>" href="/returns">
        <span>Returns</span>
        <strong>&amp; Orders</strong>
      </a>
      <a class="amz-cart<?php echo $onCart ? " active" : ""; ?>" href="/cart">
        <svg class="amz-cart-icon" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.8h7.4a2 2 0 0 0 2-1.6L20 7H6"/>
          <circle cx="9" cy="21" r="1.6"/><circle cx="17" cy="21" r="1.6"/>
        </svg>
        <span class="amz-cart-badge" id="cart-count"><?php echo (int) $cartCount; ?></span>
        <span class="amz-cart-label">Cart</span>
      </a>
    </div>
  </div>

  <nav class="amz-nav">
    <div class="amz-nav-inner">
      <div class="amz-departments">
        <button id="nav-toggle" class="amz-nav-toggle" type="button" aria-expanded="false">&#9776; All</button>
        <ul class="amz-dropdown" id="nav-menu">
          <li class="amz-dropdown-title">Departments</li>
          <li><a href="/products">All Products</a></li>
          <?php foreach ($categoriesForNav as $c) { ?>
            <li><a href="/products?category=<?php echo (int) $c["id"]; ?>"><?php echo e($c["name"]); ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <div class="amz-nav-links">
        <?php foreach ($navLinks as $label => $href) { ?>
          <a href="<?php echo e($href); ?>"<?php echo is_active($href) ? " class='active'" : ""; ?>><?php echo e($label); ?></a>
        <?php } ?>
      </div>
      <div class="amz-nav-promo">
        <a href="/deals?category=1">Shop deals in Electronics</a>
      </div>
    </div>
  </nav>
</header>
<main class="amz-main">
<?php if (!empty($_SESSION["flash"])) { ?>
  <div class="amz-alert"><?php echo e($_SESSION["flash"]); ?></div>
  <?php unset($_SESSION["flash"]); ?>
<?php } ?>
