<h1>Register</h1>
<form method="POST" action="/register">
    <label for="username">Username</label>
    <input type="text" name="username" id="username" required>
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password (min 8 characters)</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Register</button>
</form>
<p><a href="/login">Already have an account?</a></p>
