<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Auth System' ?></title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .flash { padding: 0.5rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .flash-success { background: #d4edda; color: #155724; }
        .flash-error { background: #f8d7da; color: #721c24; }
        .page-header { display: flex; justify-content: space-between; align-items: center; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0; }
        .stat-card { text-align: center; padding: 1.5rem; background: #f4f4f4; border-radius: 8px; }
        .stat-card h3 { font-size: 2rem; margin: 0; }
        .badge { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; }
        .badge-admin { background: #cce5ff; color: #004085; }
        .badge-editor { background: #e2d5f3; color: #5a2d82; }
        .badge-user { background: #e2e3e5; color: #383d41; }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-suspended { background: #f8d7da; color: #721c24; }
        .admin-nav { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .admin-nav a { text-decoration: none; padding: 0.4rem 0.8rem; border-radius: 4px; background: #f4f4f4; }
        .admin-nav a:hover { background: #e0e0e0; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/">Auth System</a>
            <?php if (\App\Core\Session::has('user_id')): ?>
                <a href="/profile">Profile</a>
                <?php if (\App\Core\Session::get('user_role') === 'admin'): ?>
                    <a href="/admin">Admin</a>
                <?php endif; ?>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php if ($f = \App\Core\Session::flash('success')): ?>
            <div class="flash flash-success"><?= htmlspecialchars($f) ?></div>
        <?php endif; ?>
        <?php if ($f = \App\Core\Session::flash('error')): ?>
            <div class="flash flash-error"><?= htmlspecialchars($f) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
