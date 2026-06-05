<?php $title = 'Manage Permissions'; ?>
<div class="page-header">
    <h1>Manage Permissions</h1>
</div>

<form method="POST" action="/admin/permissions" style="display:flex;gap:0.5rem;margin-bottom:1rem">
    <input type="text" name="name" placeholder="Permission name (e.g. posts.create)" required>
    <input type="text" name="description" placeholder="Description">
    <button type="submit">Create Permission</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($permissions as $perm): ?>
            <tr>
                <td><?= $perm->id ?></td>
                <td><?= htmlspecialchars($perm->name) ?></td>
                <td><?= htmlspecialchars($perm->description ?? '') ?></td>
                <td>
                    <form method="POST" action="/admin/permissions/<?= $perm->id ?>/delete"
                          onsubmit="return confirm('Delete permission?')" style="display:inline">
                        <button type="submit" class="button-small button-error">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
