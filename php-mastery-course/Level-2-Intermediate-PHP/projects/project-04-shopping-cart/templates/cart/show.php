<?php $title = 'Shopping Cart'; ?>
<h1>Shopping Cart</h1>

<?php if ($cart->isEmpty()): ?>
    <div class="empty-cart">
        <p>Your cart is empty.</p>
        <a href="/products" class="button">Browse Products</a>
    </div>
<?php else: ?>
    <?php foreach ($items as $item): ?>
        <div class="cart-item">
            <div class="cart-item-details">
                <strong><?= htmlspecialchars($item['product']->name) ?></strong>
                <p>$<?= number_format($item['product']->price, 2) ?> each</p>
            </div>
            <div>
                <form method="POST" action="/cart/update" class="qty-form">
                    <input type="hidden" name="product_id" value="<?= $item['product']->id ?>">
                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="0" max="<?= $item['product']->stock ?>">
                    <button type="submit">Update</button>
                </form>
                <p style="text-align:right"><strong>$<?= number_format($item['subtotal'], 2) ?></strong></p>
                <form method="POST" action="/cart/remove">
                    <input type="hidden" name="product_id" value="<?= $item['product']->id ?>">
                    <button type="submit" class="button-error">Remove</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="cart-total">
        Total: $<?= number_format($total, 2) ?>
    </div>

    <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
        <a href="/products" class="button">Continue Shopping</a>
        <?php if (\App\Core\Session::has('user_id')): ?>
            <form method="POST" action="/checkout">
                <button type="submit" class="button-primary">Checkout</button>
            </form>
        <?php else: ?>
            <a href="/login" class="button-primary">Login to Checkout</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
