<?php $title = 'Profile'; ?>
<h1>My Profile</h1>

<form method="POST" action="/profile/update">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= htmlspecialchars($user->username) ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email) ?>" required>

    <p><strong>Role:</strong> <span class="badge badge-<?= $user->role ?>"><?= $user->role ?></span></p>
    <p><strong>Status:</strong> <span class="badge badge-<?= $user->status ?>"><?= $user->status ?></span></p>
    <p><strong>Member since:</strong> <?= date('M j, Y', strtotime($user->created_at)) ?></p>

    <button type="submit">Update Profile</button>
</form>

<hr>

<h2>Change Password</h2>
<form method="POST" action="/profile/password">
    <label for="current_password">Current Password</label>
    <input type="password" name="current_password" id="current_password" required>

    <label for="new_password">New Password (min 8 characters)</label>
    <input type="password" name="new_password" id="new_password" required>

    <button type="submit">Change Password</button>
</form>
