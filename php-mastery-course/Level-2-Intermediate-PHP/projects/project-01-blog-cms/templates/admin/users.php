<?php $title = 'Manage Users'; ?>
<div class="admin-section">
    <h1>Manage Users</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
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
                                    <option value="user" <?= $user->role === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="editor" <?= $user->role === 'editor' ? 'selected' : '' ?>>Editor</option>
                                    <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        <?php else: ?>
                            <span class="badge badge-<?= $user->role ?>"><?= $user->role ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M j, Y', strtotime($user->created_at)) ?></td>
                    <td>
                        <?php if ($user->id !== (int) \App\Core\Session::get('user_id')): ?>
                            <form method="POST" action="/admin/users/<?= $user->id ?>/delete" style="display:inline">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this user and all their content?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
