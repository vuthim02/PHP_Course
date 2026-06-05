<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Blog CMS' ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="brand">BlogCMS</a>
            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <?php if (\App\Core\Session::has('user_id')): ?>
                    <li><a href="/posts/create">New Post</a></li>
                    <?php if (\App\Core\Session::get('user_role') === 'admin'): ?>
                        <li><a href="/admin">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="/logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="container">
        <?php if ($flash = \App\Core\Session::flash('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <?php if ($flash = \App\Core\Session::flash('error')): ?>
            <div class="alert alert-error"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <?php if ($errors = \App\Core\Session::errors()): ?>
            <?php foreach ($errors as $field => $msgs): ?>
                <?php foreach ($msgs as $msg): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($msg) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> BlogCMS. PHP Mastery Course - Level 2</p>
        </div>
    </footer>
</body>
</html>
