<h1>Login</h1>
<form method="POST" action="/login">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>
    <label>
        <input type="checkbox" name="remember_me" value="1"> Remember Me
    </label>
    <button type="submit">Login</button>
</form>
<p><a href="/forgot-password">Forgot Password?</a> &middot; <a href="/register">Register</a></p>
