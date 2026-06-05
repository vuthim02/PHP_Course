<?php $title = 'Manage Comments'; ?>
<div class="admin-section">
    <h1>Manage Comments</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Comment</th>
                <th>Author</th>
                <th>Post</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($comments as $comment): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($comment->content, 0, 100)) ?>...</td>
                    <td><?= htmlspecialchars($comment->username) ?></td>
                    <td><?= htmlspecialchars($comment->post_title ?? '') ?></td>
                    <td><span class="badge badge-<?= $comment->status ?>"><?= $comment->status ?></span></td>
                    <td><?= date('M j, Y', strtotime($comment->created_at)) ?></td>
                    <td class="actions">
                        <?php if ($comment->status === 'pending'): ?>
                            <a href="/admin/comments/<?= $comment->id ?>/approve" class="btn btn-sm">Approve</a>
                        <?php endif; ?>
                        <form method="POST" action="/comments/<?= $comment->id ?>/delete" style="display:inline">
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
