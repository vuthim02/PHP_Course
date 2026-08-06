<?php
// admin/_nav.php — admin panel sub-navigation.
$uri = $_SERVER["REQUEST_URI"] ?? "";
?>
<nav class="amz-admin-nav">
  <a href="/admin"<?php echo is_active("/admin") ? " class='active'" : ""; ?>>Dashboard</a>
  <a href="/admin/products"<?php echo str_starts_with($uri, "/admin/products") ? " class='active'" : ""; ?>>Products</a>
  <a href="/admin/categories"<?php echo is_active("/admin/categories") ? " class='active'" : ""; ?>>Categories</a>
  <a href="/admin/orders"<?php echo str_starts_with($uri, "/admin/orders") ? " class='active'" : ""; ?>>Orders</a>
  <a href="/admin/returns"<?php echo str_starts_with($uri, "/admin/returns") ? " class='active'" : ""; ?>>Returns</a>
  <a href="/admin/users"<?php echo is_active("/admin/users") ? " class='active'" : ""; ?>>Users</a>
  <a href="/admin/reviews"<?php echo is_active("/admin/reviews") ? " class='active'" : ""; ?>>Reviews</a>
  <a href="/" class="amz-admin-back">&#8592; Back to store</a>
</nav>
