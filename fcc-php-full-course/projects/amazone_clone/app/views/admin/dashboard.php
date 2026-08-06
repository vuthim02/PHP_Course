<?php
// admin/dashboard.php — stats + sales chart + quick lists for the admin home.
$maxRev = max(1.0, max(array_column($salesChart, "revenue")));
include __DIR__ . "/../layout/header.php";
include __DIR__ . "/_nav.php";
?>
<h1 class="amz-page-title">Admin Dashboard</h1>

<div class="amz-admin-stats">
  <div class="amz-admin-stat">
    <span class="amz-admin-stat-num"><?php echo number_format($stats["products"]); ?></span>
    <span class="amz-admin-stat-label">Products</span>
  </div>
  <div class="amz-admin-stat">
    <span class="amz-admin-stat-num"><?php echo number_format($stats["orders"]); ?></span>
    <span class="amz-admin-stat-label">Orders</span>
  </div>
  <div class="amz-admin-stat">
    <span class="amz-admin-stat-num"><?php echo money($stats["revenue"]); ?></span>
    <span class="amz-admin-stat-label">Revenue</span>
  </div>
  <div class="amz-admin-stat <?php echo $stats["low_stock"] > 0 ? "warn" : ""; ?>">
    <span class="amz-admin-stat-num"><?php echo $stats["low_stock"]; ?></span>
    <span class="amz-admin-stat-label">Low stock</span>
  </div>
  <div class="amz-admin-stat <?php echo $stats["returns"] > 0 ? "warn" : ""; ?>">
    <span class="amz-admin-stat-num"><?php echo $stats["returns"]; ?></span>
    <span class="amz-admin-stat-label">Pending returns</span>
  </div>
</div>

<section class="amz-admin-card">
  <h2>Sales &mdash; last 14 days</h2>
  <?php if ($maxRev <= 1.0 && $stats["orders"] === 0) { ?>
    <p class="amz-sum-note">No paid orders yet. Once customers check out you'll see daily sales here.</p>
  <?php } else { ?>
    <div class="amz-chart">
      <?php foreach ($salesChart as $bar) { ?>
        <div class="amz-chart-col">
          <div class="amz-chart-track">
            <div class="amz-chart-bar" style="height: <?php echo (int) round($bar["revenue"] / $maxRev * 100); ?>%"
                 title="<?php echo e($bar["day"]); ?> &mdash; <?php echo money($bar["revenue"]); ?>"></div>
          </div>
          <span class="amz-chart-label"><?php echo e(date("M j", strtotime($bar["day"]))); ?></span>
        </div>
      <?php } ?>
    </div>
  <?php } ?>
</section>

<div class="amz-admin-cols">
  <section class="amz-admin-card">
    <h2>Recent orders</h2>
    <?php if (count($recentOrders) === 0) { ?>
      <p class="amz-sum-note">No orders yet.</p>
    <?php } else { ?>
      <table class="amz-admin-table">
        <thead>
          <tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $o) { ?>
            <tr>
              <td><?php echo (int) $o["id"]; ?></td>
              <td><?php echo e($o["user_name"] ?? "Guest"); ?></td>
              <td><?php echo money($o["total"]); ?></td>
              <td><span class="amz-status amz-status-<?php echo e($o["status"]); ?>"><?php echo e($o["status"]); ?></span></td>
              <td><a href="/admin/orders/<?php echo (int) $o["id"]; ?>">View</a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </section>

  <section class="amz-admin-card">
    <h2>Top sellers</h2>
    <?php if (count($topSellers) === 0) { ?>
      <p class="amz-sum-note">No sales yet.</p>
    <?php } else { ?>
      <table class="amz-admin-table">
        <thead><tr><th>Product</th><th>Sold</th></tr></thead>
        <tbody>
          <?php foreach ($topSellers as $p) { ?>
            <tr>
              <td><a href="/product/<?php echo e($p["slug"]); ?>"><?php echo e($p["name"]); ?></a></td>
              <td><?php echo (int) $p["sold"]; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </section>
</div>

<div class="amz-admin-cols">
  <section class="amz-admin-card">
    <h2>Low stock (<?php echo count($lowStock); ?>)</h2>
    <?php if (count($lowStock) === 0) { ?>
      <p class="amz-sum-note">All products are well stocked. &#128077;</p>
    <?php } else { ?>
      <table class="amz-admin-table">
        <thead><tr><th>Product</th><th>Stock</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($lowStock as $p) { ?>
            <tr>
              <td><?php echo e($p["name"]); ?></td>
              <td class="amz-stock-warn"><?php echo (int) $p["stock"]; ?></td>
              <td><a href="/admin/products/<?php echo (int) $p["id"]; ?>/edit">Restock</a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </section>

  <section class="amz-admin-card">
    <h2>Newest return requests</h2>
    <?php if (count($recentReturns) === 0) { ?>
      <p class="amz-sum-note">No returns requested.</p>
    <?php } else { ?>
      <table class="amz-admin-table">
        <thead><tr><th>Return</th><th>Order</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($recentReturns as $r) { ?>
            <tr>
              <td><a href="/admin/returns/<?php echo (int) $r["id"]; ?>">#<?php echo (int) $r["id"]; ?></a></td>
              <td>#<?php echo (int) $r["order_id"]; ?></td>
              <td><span class="amz-status amz-status-<?php echo e($r["status"]); ?>"><?php echo e($r["status"]); ?></span></td>
              <td><a href="/admin/returns/<?php echo (int) $r["id"]; ?>">Manage</a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </section>
</div>

<section class="amz-admin-card">
  <h2>Latest reviews</h2>
  <?php if (count($recentReviews) === 0) { ?>
    <p class="amz-sum-note">No reviews yet.</p>
  <?php } else { ?>
    <table class="amz-admin-table">
      <thead><tr><th>Product</th><th>Rating</th><th>By</th><th>Comment</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recentReviews as $r) { ?>
          <tr>
            <td><a href="/product/<?php echo e($r["product_slug"]); ?>"><?php echo e($r["product_name"]); ?></a></td>
            <td><?php echo stars((float) $r["rating"]); ?></td>
            <td><?php echo e($r["user_name"]); ?></td>
            <td><?php echo e(mb_strimwidth($r["comment"] ?? "", 0, 60, "…")); ?></td>
            <td><a href="/admin/reviews">Moderate</a></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  <?php } ?>
</section>

<?php include __DIR__ . "/../layout/footer.php"; ?>
