<?php $title = 'Admin Dashboard'; ?>
<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?= $postCount ?></h3>
            <p>Posts</p>
        </div>
        <div class="stat-card">
            <h3><?= $userCount ?></h3>
            <p>Users</p>
        </div>
        <div class="stat-card">
            <h3><?= $commentCount ?></h3>
            <p>Comments</p>
        </div>
        <div class="stat-card">
            <h3><?= $categoryCount ?></h3>
            <p>Categories</p>
        </div>
    </div>
    <nav class="admin-nav">
        <a href="/admin/posts" class="btn">Manage Posts</a>
        <a href="/admin/comments" class="btn">Manage Comments</a>
        <a href="/admin/users" class="btn">Manage Users</a>
        <a href="/admin/categories" class="btn">Manage Categories</a>
    </nav>
</div>
