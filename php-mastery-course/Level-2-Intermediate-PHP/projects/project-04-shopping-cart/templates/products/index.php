<?php $title = 'Products'; ?>
<div class="page-header">
    <h1>Products</h1>
</div>

<div class="products-grid">
    <?php foreach ($pagination['items'] as $product): ?>
        <div class="product-card">
            <h3><a href="/products/<?= htmlspecialchars($product->slug) ?>"><?= htmlspecialchars($product->name) ?></a></h3>
            <p class="price">$<?= $product->formattedPrice() ?></p>
            <p><span class="stock-badge <?= $product->isInStock() ? 'in-stock' : 'out-of-stock' ?>">
                <?= $product->isInStock() ? 'In Stock (' . $product->stock . ')' : 'Out of Stock' ?>
            </span></p>
            <p><?= htmlspecialchars(substr($product->description, 0, 120)) ?>...</p>
            <a href="/products/<?= htmlspecialchars($product->slug) ?>" class="button">View Details</a>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($pagination['pages'] > 1): ?>
    <div style="text-align:center;margin-top:1rem;">
        <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="button <?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
