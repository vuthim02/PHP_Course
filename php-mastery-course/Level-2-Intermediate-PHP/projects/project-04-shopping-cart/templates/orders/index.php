<?php $title = 'My Orders'; ?>
<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <p>No orders yet. <a href="/products">Start shopping</a>.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order->id ?></td>
                    <td>$<?= number_format($order->total, 2) ?></td>
                    <td><?= $order->status ?></td>
                    <td><?= date('M j, Y g:i a', strtotime($order->created_at)) ?></td>
                    <td><a href="/orders/<?= $order->id ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
