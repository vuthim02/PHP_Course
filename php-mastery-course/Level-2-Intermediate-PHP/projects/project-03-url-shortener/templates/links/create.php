<?php $title = 'Create Short Link'; ?>
<h1>Create Short Link</h1>
<form method="POST" action="/links">
    <label for="long_url">Long URL</label>
    <input type="url" name="long_url" id="long_url" placeholder="https://example.com/very/long/url" required>

    <label for="custom_code">Custom Code (optional)</label>
    <input type="text" name="custom_code" id="custom_code" placeholder="my-custom-link">

    <label for="expires_at">Expires At (optional)</label>
    <input type="date" name="expires_at" id="expires_at">

    <button type="submit">Create Short Link</button>
</form>
