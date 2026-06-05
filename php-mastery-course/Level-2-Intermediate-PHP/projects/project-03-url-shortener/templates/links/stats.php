<?php $title = 'Link Stats'; ?>
<h1>Link Stats</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= count($clicks) ?></h3>
        <p>Total Clicks</p>
    </div>
    <div class="stat-card">
        <h3><?= count($referers) ?></h3>
        <p>Referrers</p>
    </div>
    <div class="stat-card">
        <h3><?= $link->isExpired() ? 'Expired' : 'Active' ?></h3>
        <p>Status</p>
    </div>
</div>

<p>
    <strong>Short URL:</strong>
    <a href="/<?= htmlspecialchars($link->short_code) ?>"><?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($link->short_code) ?></a><br>
    <strong>Long URL:</strong> <a href="<?= htmlspecialchars($link->long_url) ?>"><?= htmlspecialchars($link->long_url) ?></a><br>
    <strong>Created:</strong> <?= date('M j, Y g:i a', strtotime($link->created_at)) ?><br>
    <?php if ($link->expires_at): ?>
        <strong>Expires:</strong> <?= date('M j, Y', strtotime($link->expires_at)) ?>
    <?php endif; ?>
</p>

<h2>Recent Clicks</h2>
<?php if (empty($clicks)): ?>
    <p>No clicks yet.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>IP</th>
                <th>Referer</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clicks as $click): ?>
                <tr>
                    <td><?= date('M j, Y g:i a', strtotime($click->clicked_at)) ?></td>
                    <td><?= htmlspecialchars($click->ip_address) ?></td>
                    <td><?= htmlspecialchars(substr($click->referer, 0, 50)) ?></td>
                    <td style="font-size:0.8rem"><?= htmlspecialchars(substr($click->user_agent, 0, 60)) ?>...</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Top Referrers</h3>
    <table>
        <thead><tr><th>Referer</th><th>Clicks</th></tr></thead>
        <tbody>
            <?php foreach ($referers as $ref): ?>
                <tr>
                    <td><?= htmlspecialchars($ref->referer ?: 'Direct') ?></td>
                    <td><?= $ref->count ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a href="/links" class="button">&larr; Back to Links</a>
