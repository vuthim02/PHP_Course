<?php

declare(strict_types=1);

define('DATA_FILE', __DIR__ . '/messages.json');

function loadEntries(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(DATA_FILE);
    if ($data === false) {
        return [];
    }
    $entries = json_decode($data, true);
    return is_array($entries) ? $entries : [];
}

function loadSingleEntry(int $id): ?array
{
    $entries = loadEntries();
    foreach ($entries as $e) {
        if ($e['id'] === $id) {
            return $e;
        }
    }
    return null;
}

$entries = loadEntries();

// Sort newest first
usort($entries, fn(array $a, array $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

// Flash message
$flash = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📖 Guestbook</h1>
        <p class="subtitle">Leave a message for the world to see.</p>

        <?php if ($flash === 'signed'): ?>
            <div class="alert success">Thank you! Your entry has been added.</div>
        <?php elseif ($flash === 'deleted'): ?>
            <div class="alert success">Entry deleted.</div>
        <?php elseif ($flash === 'error'): ?>
            <div class="alert error">Something went wrong. Please try again.</div>
        <?php endif; ?>

        <div class="form-card">
            <h2>Sign the Guestbook</h2>
            <form action="sign.php" method="post">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" name="name" id="name" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea name="message" id="message" rows="4" required maxlength="2000"></textarea>
                </div>
                <button type="submit">Sign Guestbook</button>
            </form>
        </div>

        <div class="entries">
            <h2>Entries (<?= count($entries) ?>)</h2>
            <?php if (empty($entries)): ?>
                <p class="empty">No entries yet. Be the first to sign!</p>
            <?php else: ?>
                <?php foreach ($entries as $entry): ?>
                    <div class="entry">
                        <div class="entry-header">
                            <strong><?= htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="entry-date"><?= htmlspecialchars($entry['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="entry-body">
                            <?= nl2br(htmlspecialchars($entry['message'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <p class="admin-link"><a href="admin.php">Admin Panel</a></p>
    </div>
</body>
</html>
