<?php $title = htmlspecialchars($product->name); ?>
<h1><?= htmlspecialchars($product->name) ?></h1>
<p class="price">$<?= $product->formattedPrice() ?></p>
<p><span class="stock-badge <?= $product->isInStock() ? 'in-stock' : 'out-of-stock' ?>">
    <?= $product->isInStock() ? 'In Stock (' . $product->stock . ')' : 'Out of Stock' ?>
</span></p>
<p><?= nl2br(htmlspecialchars($product->description)) ?></p>

<?php if ($product->isInStock()): ?>
    <form method="POST" action="/cart/add">
        <input type="hidden" name="product_id" value="<?= $product->id ?>">
        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product->stock ?>">
        <button type="submit">Add to Cart</button>
    </form>
<?php else: ?>
    <p><strong>Out of Stock</strong></p>
<?php endif; ?>

<a href="/products" class="button">&larr; Back to Products</a>
