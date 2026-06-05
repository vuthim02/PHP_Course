<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Shop' ?></title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .product-card { background: #fff; border-radius: 8px; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .product-card h3 { margin: 0.5rem 0; }
        .price { font-size: 1.25rem; font-weight: bold; color: #2c3e50; }
        .stock-badge { font-size: 0.8rem; padding: 0.15rem 0.4rem; border-radius: 4px; }
        .in-stock { background: #d4edda; color: #155724; }
        .out-of-stock { background: #f8d7da; color: #721c24; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #eee; }
        .cart-item-details { flex: 1; }
        .cart-total { text-align: right; font-size: 1.25rem; font-weight: bold; margin: 1rem 0; }
        .qty-form { display: flex; gap: 0.25rem; align-items: center; }
        .qty-form input { width: 60px; }
        .flash { padding: 0.5rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .flash-success { background: #d4edda; color: #155724; }
        .flash-error { background: #f8d7da; color: #721c24; }
        .page-header { display: flex; justify-content: space-between; align-items: center; }
        .empty-cart { text-align: center; padding: 3rem; color: #666; }
        .order-item { padding: 0.5rem 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/">Shop</a>
            <a href="/products">Products</a>
            <a href="/cart">Cart <?php if (isset($_SESSION['cart']) && array_sum($_SESSION['cart']) > 0): ?>(<?= array_sum($_SESSION['cart']) ?>)<?php endif; ?></a>
            <?php if (\App\Core\Session::has('user_id')): ?>
                <a href="/orders">Orders</a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php if ($f = \App\Core\Session::flash('success')): ?>
            <div class="flash flash-success"><?= htmlspecialchars($f) ?></div>
        <?php endif; ?>
        <?php if ($f = \App\Core\Session::flash('error')): ?>
            <div class="flash flash-error"><?= htmlspecialchars($f) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
