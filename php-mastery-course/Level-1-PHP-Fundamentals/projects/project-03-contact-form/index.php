<?php

declare(strict_types=1);

define('DATA_FILE', __DIR__ . '/submissions.json');
define('CSRF_TOKEN_KEY', 'contact_csrf_token');

// ---------- Helpers ----------

function loadSubmissions(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(DATA_FILE);
    return is_array($decoded = json_decode($data, true)) ? $decoded : [];
}

function saveSubmission(array $submission): void
{
    $entries = loadSubmissions();
    $entries[] = $submission;
    file_put_contents(
        DATA_FILE,
        json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function generateCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION[CSRF_TOKEN_KEY] = $token;
    return $token;
}

function verifyCsrfToken(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION[CSRF_TOKEN_KEY]) && hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
}

// ---------- State ----------

$errors = [];
$old    = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    $token = $_POST['_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        $errors['_token'] = 'Invalid or expired form token. Please try again.';
    }

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $old = compact('name', 'email', 'subject', 'message');

    // Validation
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be 100 characters or fewer.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($subject === '') {
        $errors['subject'] = 'Subject is required.';
    } elseif (strlen($subject) < 3) {
        $errors['subject'] = 'Subject must be at least 3 characters.';
    } elseif (strlen($subject) > 200) {
        $errors['subject'] = 'Subject must be 200 characters or fewer.';
    }

    if ($message === '') {
        $errors['message'] = 'Message is required.';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    } elseif (strlen($message) > 5000) {
        $errors['message'] = 'Message must be 5000 characters or fewer.';
    }

    if (empty($errors)) {
        saveSubmission([
            'id'         => uniqid('msg_', true),
            'name'       => $name,
            'email'      => $email,
            'subject'    => $subject,
            'message'    => $message,
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Regenerate token after successful submission
        generateCsrfToken();
        $success = true;
        $old = [];
    }
}

// Generate fresh token for the form
$csrfToken = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Contact Us</h1>
        <p class="subtitle">We'd love to hear from you. Fill out the form below.</p>

        <?php if ($success): ?>
            <div class="alert success">
                <strong>Thank you!</strong> Your message has been sent successfully.
                <a href="">Send another &rarr;</a>
            </div>
        <?php endif; ?>

        <form method="post" class="contact-form" novalidate>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <?php if (isset($errors['_token'])): ?>
                <div class="alert error"><?= htmlspecialchars($errors['_token'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="form-group <?= isset($errors['name']) ? 'has-error' : '' ?>">
                <label for="name">Name *</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="100">
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
                <?php if (isset($errors['email'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['subject']) ? 'has-error' : '' ?>">
                <label for="subject">Subject *</label>
                <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($old['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="200">
                <?php if (isset($errors['subject'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['subject'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group <?= isset($errors['message']) ? 'has-error' : '' ?>">
                <label for="message">Message *</label>
                <textarea name="message" id="message" rows="6" maxlength="5000"><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (isset($errors['message'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Send Message</button>
        </form>
    </div>
</body>
</html>
