<div class="hero">
    <h1>URL Shortener</h1>
    <p>Paste a long URL and get a short, shareable link.</p>

    <form method="POST" action="/links" class="shorten-form">
        <div style="display:flex;gap:0.5rem;">
            <input type="url" name="long_url" placeholder="https://example.com/very/long/url" required style="flex:1">
            <button type="submit">Shorten</button>
        </div>
        <div style="display:flex;gap:0.5rem;margin-top:0.5rem;">
            <input type="text" name="custom_code" placeholder="Custom code (optional)" style="flex:1">
            <input type="date" name="expires_at">
        </div>
        <p style="margin-top:0.5rem;font-size:0.85rem;">
            <a href="/links">My Links</a> &middot; <a href="/login">Login</a> to manage your links.
        </p>
    </form>
</div>
