<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Task Manager' ?></title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .board-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .lists-container { display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; }
        .list-column { background: #f4f4f4; border-radius: 8px; padding: 0.75rem; min-width: 280px; max-width: 320px; }
        .list-column h3 { margin-top: 0; padding-bottom: 0.5rem; border-bottom: 2px solid #ddd; }
        .card-item { background: #fff; border-radius: 6px; padding: 0.75rem; margin-bottom: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-item h4 { margin: 0 0 0.25rem 0; }
        .card-meta { font-size: 0.8rem; color: #666; }
        .card-actions { display: flex; gap: 0.25rem; margin-top: 0.5rem; }
        .card-actions form { display: inline; }
        .card-actions button, .card-actions a { font-size: 0.75rem; padding: 0.15rem 0.4rem; }
        .inline-form { display: flex; gap: 0.5rem; margin-bottom: 0.5rem; }
        .inline-form input, .inline-form select { padding: 0.3rem; font-size: 0.85rem; }
        .board-card { background: #fff; border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .overdue { border-left: 4px solid #e74c3c; }
        .assigned { font-size: 0.8rem; color: #3498db; }
        .flash { padding: 0.5rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .flash-success { background: #d4edda; color: #155724; }
        .flash-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/">Task Manager</a>
            <a href="/boards">My Boards</a>
            <?php if (\App\Core\Session::has('user_id')): ?>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if ($flash = \App\Core\Session::flash('success')): ?>
            <div class="flash flash-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = \App\Core\Session::flash('error')): ?>
            <div class="flash flash-error"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
