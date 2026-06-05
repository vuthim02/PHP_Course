<h1>Reset Password</h1>
<form method="POST" action="/reset-password">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <label for="password">New Password (min 8 characters)</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Reset Password</button>
</form>
