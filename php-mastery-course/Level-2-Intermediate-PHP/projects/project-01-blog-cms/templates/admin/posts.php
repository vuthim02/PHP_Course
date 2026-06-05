<?php $title = 'Manage Posts'; ?>
<div class="admin-section">
    <div class="page-header">
        <h1>Manage Posts</h1>
        <a href="/posts/create" class="btn btn-primary">New Post</a>
    </div>

    <div class="filter-bar">
        <a href="/admin/posts" class="btn btn-sm <?= !isset($_GET['status']) ? 'active' : '' ?>">All</a>
        <a href="/admin/posts?status=published" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'published' ? 'active' : '' ?>">Published</a>
        <a href="/admin/posts?status=draft" class="btn btn-sm <?= ($_GET['status'] ?? '') === 'draft' ? 'active' : '' ?>">Draft</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post->title) ?></td>
                    <td><?= htmlspecialchars($post->username) ?></td>
                    <td><span class="badge badge-<?= $post->status ?>"><?= $post->status ?></span></td>
                    <td><?= date('M j, Y', strtotime($post->created_at)) ?></td>
                    <td class="actions">
                        <a href="/posts/<?= htmlspecialchars($post->slug) ?>" class="btn btn-sm">View</a>
                        <?php if ($post->user_id === (int) \App\Core\Session::get('user_id')): ?>
                            <a href="/posts/<?= $post->id ?>/edit" class="btn btn-sm">Edit</a>
                            <form method="POST" action="/posts/<?= $post->id ?>/delete" style="display:inline">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this post?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
