<?php $title = 'My Links'; ?>
<div class="page-header">
    <h1>My Links</h1>
    <a href="/links/create" class="button">New Link</a>
</div>

<?php if (empty($links)): ?>
    <p class="empty">No links yet. <a href="/links/create">Create your first short link</a>.</p>
<?php else: ?>
    <table class="link-table">
        <thead>
            <tr>
                <th>Short URL</th>
                <th>Long URL</th>
                <th>Clicks</th>
                <th>Expires</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $link): ?>
                <tr>
                    <td>
                        <a href="/<?= htmlspecialchars($link->short_code) ?>" class="short-url" target="_blank">
                            <?= $_SERVER['HTTP_HOST'] ?>/<?= htmlspecialchars($link->short_code) ?>
                        </a>
                    </td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <a href="<?= htmlspecialchars($link->long_url) ?>" target="_blank"><?= htmlspecialchars($link->long_url) ?></a>
                    </td>
                    <td><?= $link->clicks ?></td>
                    <td><?= $link->expires_at ? date('M j, Y', strtotime($link->expires_at)) : 'Never' ?></td>
                    <td>
                        <a href="/links/<?= $link->id ?>/stats" class="button button-small">Stats</a>
                        <form method="POST" action="/links/<?= $link->id ?>/delete" style="display:inline" onsubmit="return confirm('Delete?')">
                            <button type="submit" class="button-small button-error">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
