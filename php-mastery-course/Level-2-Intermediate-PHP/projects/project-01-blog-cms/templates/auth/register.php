<div class="auth-form">
    <h1>Register</h1>
    <form method="POST" action="/register">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars(\App\Core\Session::old('username', '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars(\App\Core\Session::old('email', '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password (min 8 characters)</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
        <p>Already have an account? <a href="/login">Login</a></p>
    </form>
</div>
