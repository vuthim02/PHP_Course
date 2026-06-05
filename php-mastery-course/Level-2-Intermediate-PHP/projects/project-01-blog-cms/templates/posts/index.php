<?php $title = 'Blog Posts'; ?>
<div class="posts-page">
    <div class="page-header">
        <h1>Blog Posts</h1>
        <?php if (isset($category)): ?>
            <p class="category-label">Category: <?= htmlspecialchars($category->name) ?></p>
        <?php endif; ?>
    </div>

    <?php if (empty($posts)): ?>
        <p class="empty-state">No posts yet.</p>
    <?php endif; ?>

    <div class="posts-grid">
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <h2><a href="/posts/<?= htmlspecialchars($post->slug) ?>"><?= htmlspecialchars($post->title) ?></a></h2>
                <div class="post-meta">
                    <span>By <?= htmlspecialchars($post->username) ?></span>
                    <span><?= date('M j, Y', strtotime($post->created_at)) ?></span>
                    <?php if (isset($post->category_name)): ?>
                        <span class="category"><?= htmlspecialchars($post->category_name) ?></span>
                    <?php endif; ?>
                </div>
                <p class="post-excerpt"><?= htmlspecialchars($post->excerpt ?? substr(strip_tags($post->content), 0, 200) . '...') ?></p>
                <a href="/posts/<?= htmlspecialchars($post->slug) ?>" class="btn btn-sm">Read More</a>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $pagination['currentPage'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
