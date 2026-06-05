<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'URL Shortener' ?></title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .hero { text-align: center; padding: 3rem 0; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .hero p { color: #666; font-size: 1.1rem; }
        .shorten-form { max-width: 600px; margin: 0 auto; }
        .link-table { width: 100%; }
        .link-table td, .link-table th { padding: 0.5rem; vertical-align: middle; }
        .short-url { font-family: monospace; font-weight: bold; color: #3498db; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin: 1rem 0; }
        .stat-card { text-align: center; padding: 1.5rem; background: #f4f4f4; border-radius: 8px; }
        .stat-card h3 { font-size: 2rem; margin: 0; }
        .flash { padding: 0.5rem 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .flash-success { background: #d4edda; color: #155724; }
        .flash-error { background: #f8d7da; color: #721c24; }
        .page-header { display: flex; justify-content: space-between; align-items: center; }
        .empty { text-align: center; padding: 3rem; color: #666; }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="/">URL Shortener</a>
            <?php if (\App\Core\Session::has('user_id')): ?>
                <a href="/links">My Links</a>
                <a href="/links/create">New Link</a>
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
