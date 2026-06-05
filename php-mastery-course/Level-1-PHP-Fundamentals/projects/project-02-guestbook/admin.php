<?php

declare(strict_types=1);

define('DATA_FILE', __DIR__ . '/messages.json');

function loadEntries(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(DATA_FILE);
    return is_array($decoded = json_decode($data, true)) ? $decoded : [];
}

function saveEntries(array $entries): void
{
    file_put_contents(
        DATA_FILE,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];
    $entries = loadEntries();
    $before = count($entries);

    $entries = array_values(
        array_filter($entries, fn(array $e) => $e['id'] !== $deleteId)
    );

    if (count($entries) < $before) {
        saveEntries($entries);
        header('Location: index.php?msg=deleted');
    } else {
        header('Location: admin.php?msg=notfound');
    }
    exit;
}

$entries = loadEntries();
usort($entries, fn(array $a, array $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

$flash = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook — Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🔧 Admin Panel</h1>
        <p><a href="index.php">&larr; Back to Guestbook</a></p>

        <?php if ($flash === 'notfound'): ?>
            <div class="alert error">Entry not found.</div>
        <?php endif; ?>

        <h2>Manage Entries (<?= count($entries) ?>)</h2>

        <?php if (empty($entries)): ?>
            <p class="empty">No entries to manage.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= $entry['id'] ?></td>
                            <td><?= htmlspecialchars($entry['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($entry['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="msg-cell"><?= htmlspecialchars(substr($entry['message'], 0, 80), ENT_QUOTES, 'UTF-8') ?><?= strlen($entry['message']) > 80 ? '…' : '' ?></td>
                            <td><?= htmlspecialchars($entry['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Delete this entry?');">
                                    <input type="hidden" name="delete_id" value="<?= $entry['id'] ?>">
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
