<div class="auth-form">
    <h1>Login</h1>
    <form method="POST" action="/login">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars(\App\Core\Session::old('email', '')) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
        <p>Don't have an account? <a href="/register">Register</a></p>
    </form>
</div>
