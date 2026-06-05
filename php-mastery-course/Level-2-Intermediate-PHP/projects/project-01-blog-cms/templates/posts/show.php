<?php $title = htmlspecialchars($post->title); ?>
<div class="post-show">
    <article>
        <h1><?= htmlspecialchars($post->title) ?></h1>
        <div class="post-meta">
            <span>By <?= htmlspecialchars($post->author()?->username ?? 'Unknown') ?></span>
            <span><?= date('M j, Y', strtotime($post->created_at)) ?></span>
            <?php if ($post->category()): ?>
                <span class="category"><?= htmlspecialchars($post->category()->name) ?></span>
            <?php endif; ?>
        </div>
        <div class="post-content">
            <?= nl2br(htmlspecialchars($post->content)) ?>
        </div>
    </article>

    <section class="comments-section">
        <h2>Comments (<?= count($comments) ?>)</h2>

        <?php if (\App\Core\Session::has('user_id')): ?>
            <form method="POST" action="/comments" class="comment-form">
                <input type="hidden" name="post_id" value="<?= $post->id ?>">
                <input type="hidden" name="post_slug" value="<?= htmlspecialchars($post->slug) ?>">
                <div class="form-group">
                    <textarea name="content" rows="3" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
            </form>
        <?php else: ?>
            <p><a href="/login">Login</a> to leave a comment.</p>
        <?php endif; ?>

        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-meta">
                    <strong><?= htmlspecialchars($comment->username) ?></strong>
                    <span><?= date('M j, Y g:i a', strtotime($comment->created_at)) ?></span>
                </div>
                <p><?= htmlspecialchars($comment->content) ?></p>
                <?php if (\App\Core\Session::get('user_id') == $comment->user_id || \App\Core\Session::get('user_role') === 'admin'): ?>
                    <form method="POST" action="/comments/<?= $comment->id ?>/delete" style="display:inline">
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this comment?')">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
</div>
