<?php $title = 'Manage Users'; ?>
<div class="page-header">
    <h1>Manage Users</h1>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user->id ?></td>
                <td><?= htmlspecialchars($user->username) ?></td>
                <td><?= htmlspecialchars($user->email) ?></td>
                <td>
                    <?php if ($user->id !== (int) \App\Core\Session::get('user_id')): ?>
                        <form method="POST" action="/admin/users/<?= $user->id ?>/role" style="display:inline">
                            <select name="role" onchange="this.form.submit()">
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role->name ?>" <?= $user->role === $role->name ? 'selected' : '' ?>>
                                        <?= $role->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php else: ?>
                        <span class="badge badge-<?= $user->role ?>"><?= $user->role ?></span>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-<?= $user->status ?>"><?= $user->status ?></span></td>
                <td><?= date('M j, Y', strtotime($user->created_at)) ?></td>
                <td>
                    <?php if ($user->id !== (int) \App\Core\Session::get('user_id')): ?>
                        <a href="/admin/users/<?= $user->id ?>/toggle" class="button button-small">
                            <?= $user->status === 'active' ? 'Suspend' : 'Activate' ?>
                        </a>
                        <form method="POST" action="/admin/users/<?= $user->id ?>/delete" style="display:inline"
                              onsubmit="return confirm('Delete user?')">
                            <button type="submit" class="button-small button-error">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
