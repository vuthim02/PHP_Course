<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

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
    $dir = dirname(DATA_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(
        DATA_FILE,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
} elseif (strlen($name) > 100) {
    $errors[] = 'Name must be 100 characters or fewer.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
}

if ($message === '') {
    $errors[] = 'Message is required.';
} elseif (strlen($message) > 2000) {
    $errors[] = 'Message must be 2000 characters or fewer.';
}

if (!empty($errors)) {
    // Store errors in session or pass via query (simplified: redirect back)
    $errorString = implode('; ', $errors);
    header('Location: index.php?msg=error&details=' . urlencode($errorString));
    exit;
}

$entries = loadEntries();

$id = count($entries) > 0 ? max(array_column($entries, 'id')) + 1 : 1;

$entries[] = [
    'id'         => $id,
    'name'       => $name,
    'email'      => $email,
    'message'    => $message,
    'created_at' => date('Y-m-d H:i:s'),
];

saveEntries($entries);

header('Location: index.php?msg=signed');
exit;
