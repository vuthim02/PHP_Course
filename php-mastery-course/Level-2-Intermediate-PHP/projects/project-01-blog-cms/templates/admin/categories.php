<?php $title = 'Manage Categories'; ?>
<div class="admin-section">
    <h1>Manage Categories</h1>

    <form method="POST" action="/admin/categories" class="inline-form">
        <input type="text" name="name" placeholder="Category name" required>
        <input type="text" name="description" placeholder="Description (optional)">
        <button type="submit" class="btn btn-primary btn-sm">Add</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Posts</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat->name) ?></td>
                    <td><?= htmlspecialchars($cat->slug) ?></td>
                    <td><?= $cat->post_count ?? 0 ?></td>
                    <td>
                        <form method="POST" action="/admin/categories/<?= $cat->id ?>/delete" style="display:inline">
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
