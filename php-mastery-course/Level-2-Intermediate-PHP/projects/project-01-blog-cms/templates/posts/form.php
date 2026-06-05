<?php $title = $post ? 'Edit Post' : 'New Post'; ?>
<div class="post-form">
    <h1><?= $title ?></h1>
    <form method="POST" action="<?= $post ? '/posts/' . $post->id . '/update' : '/posts' ?>">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($post->title ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                <option value="">-- No Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat->id ?>" <?= ($post && $post->category_id === $cat->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <input type="text" name="excerpt" id="excerpt" value="<?= htmlspecialchars($post->excerpt ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="12" required><?= htmlspecialchars($post->content ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="draft" <?= ($post && $post->status === 'draft') ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($post && $post->status === 'published') ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $post ? 'Update' : 'Create' ?> Post</button>
            <a href="/" class="btn">Cancel</a>
        </div>
    </form>
</div>
