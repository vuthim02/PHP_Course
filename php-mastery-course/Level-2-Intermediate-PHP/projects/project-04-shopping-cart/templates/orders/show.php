<?php $title = 'Order #' . $order->id; ?>
<h1>Order #<?= $order->id ?></h1>

<div style="margin-bottom:1rem">
    <strong>Status:</strong> <?= $order->status ?><br>
    <strong>Date:</strong> <?= date('M j, Y g:i a', strtotime($order->created_at)) ?><br>
    <strong>Total:</strong> $<?= number_format($order->total, 2) ?>
</div>

<h2>Items</h2>
<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item->product_name) ?></td>
                <td>$<?= number_format($item->price, 2) ?></td>
                <td><?= $item->quantity ?></td>
                <td>$<?= number_format($item->subtotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th>$<?= number_format($order->total, 2) ?></th>
        </tr>
    </tfoot>
</table>

<a href="/orders" class="button">&larr; Back to Orders</a>
