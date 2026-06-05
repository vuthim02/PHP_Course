<?php $title = 'Manage Roles'; ?>
<div class="page-header">
    <h1>Manage Roles</h1>
</div>

<form method="POST" action="/admin/roles" style="display:flex;gap:0.5rem;margin-bottom:1rem">
    <input type="text" name="name" placeholder="Role name" required>
    <input type="text" name="description" placeholder="Description">
    <button type="submit">Create Role</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Permissions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
            <tr>
                <td><?= $role->id ?></td>
                <td><?= htmlspecialchars($role->name) ?></td>
                <td><?= htmlspecialchars($role->description ?? '') ?></td>
                <td>
                    <?php foreach ($role->permissions() as $perm): ?>
                        <span style="display:inline-block;background:#e2e3e5;padding:0.1rem 0.4rem;border-radius:4px;margin:0.1rem;font-size:0.8rem;">
                            <?= htmlspecialchars($perm->name) ?>
                            <a href="/admin/roles/<?= $role->id ?>/permissions/<?= $perm->id ?>/remove" style="text-decoration:none;color:#e74c3c;">&times;</a>
                        </span>
                    <?php endforeach; ?>

                    <form method="POST" action="/admin/roles/<?= $role->id ?>/permissions" style="margin-top:0.25rem">
                        <select name="permission_id">
                            <option value="">Add permission...</option>
                            <?php foreach ($permissions as $perm): ?>
                                <option value="<?= $perm->id ?>"><?= htmlspecialchars($perm->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="button-small">Add</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="/admin/roles/<?= $role->id ?>/delete"
                          onsubmit="return confirm('Delete role?')" style="display:inline">
                        <button type="submit" class="button-small button-error">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
