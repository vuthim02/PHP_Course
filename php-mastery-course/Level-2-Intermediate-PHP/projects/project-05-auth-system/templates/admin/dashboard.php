<?php $title = 'Admin Dashboard'; ?>
<h1>Admin Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $userCount ?></h3>
        <p>Users</p>
    </div>
    <div class="stat-card">
        <h3><?= $roleCount ?></h3>
        <p>Roles</p>
    </div>
    <div class="stat-card">
        <h3><?= $permissionCount ?></h3>
        <p>Permissions</p>
    </div>
</div>

<nav class="admin-nav">
    <a href="/admin/users">Manage Users</a>
    <a href="/admin/roles">Manage Roles</a>
    <a href="/admin/permissions">Manage Permissions</a>
</nav>
