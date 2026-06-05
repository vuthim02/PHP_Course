<h1>My Boards</h1>

<div class="board-header">
    <form method="POST" action="/boards" class="inline-form">
        <input type="text" name="title" placeholder="Board title" required>
        <input type="text" name="description" placeholder="Description (optional)">
        <button type="submit">Create Board</button>
    </form>
</div>

<?php if (empty($boards)): ?>
    <p>No boards yet. Create one above!</p>
<?php endif; ?>

<div class="grid-2">
    <?php foreach ($boards as $board): ?>
        <div class="board-card">
            <h3><a href="/boards/<?= $board->id ?>"><?= htmlspecialchars($board->title) ?></a></h3>
            <?php if ($board->description): ?>
                <p><?= htmlspecialchars($board->description) ?></p>
            <?php endif; ?>
            <p><small><?= $board->card_count ?? 0 ?> cards</small></p>
            <form method="POST" action="/boards/<?= $board->id ?>/delete" onsubmit="return confirm('Delete this board?')">
                <button type="submit" class="button-error">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
