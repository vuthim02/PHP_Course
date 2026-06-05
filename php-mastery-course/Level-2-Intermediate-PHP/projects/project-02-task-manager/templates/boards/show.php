<?php $title = htmlspecialchars($board->title); ?>

<div class="board-header">
    <h1><?= htmlspecialchars($board->title) ?></h1>
    <div>
        <a href="/boards" class="button">&larr; Back</a>
        <form method="POST" action="/boards/<?= $board->id ?>/delete" style="display:inline" onsubmit="return confirm('Delete this board and all its cards?')">
            <button type="submit" class="button-error">Delete Board</button>
        </form>
    </div>
</div>

<div class="lists-container">
    <?php foreach ($lists as $list): ?>
        <div class="list-column">
            <h3><?= htmlspecialchars($list->title) ?></h3>

            <div class="cards">
                <?php foreach ($list->cards() as $card): ?>
                    <div class="card-item <?= ($card->due_date && strtotime($card->due_date) < time()) ? 'overdue' : '' ?>">
                        <h4><?= htmlspecialchars($card->title) ?></h4>
                        <?php if ($card->description): ?>
                            <p><small><?= htmlspecialchars(substr($card->description, 0, 100)) ?></small></p>
                        <?php endif; ?>
                        <div class="card-meta">
                            <?php if ($card->due_date): ?>
                                <span>Due: <?= date('M j', strtotime($card->due_date)) ?></span>
                            <?php endif; ?>
                            <?php if ($card->assigned_user_id && $card->assignedUser()): ?>
                                <span class="assigned">👤 <?= htmlspecialchars($card->assignedUser()->username) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-actions">
                            <form method="POST" action="/cards/<?= $card->id ?>/delete" onsubmit="return confirm('Delete this card?')">
                                <button type="submit">Delete</button>
                            </form>
                            <form method="POST" action="/cards/<?= $card->id ?>/move">
                                <input type="hidden" name="board_list_id" value="<?= $list->id ?>">
                                <select name="board_list_id" onchange="this.form.submit()">
                                    <?php foreach ($lists as $l): ?>
                                        <option value="<?= $l->id ?>" <?= $l->id === $list->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($l->title) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="position" value="0">
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <details style="margin-top:0.5rem">
                <summary>Add Card</summary>
                <form method="POST" action="/cards">
                    <input type="hidden" name="board_list_id" value="<?= $list->id ?>">
                    <input type="text" name="title" placeholder="Card title" required>
                    <input type="text" name="description" placeholder="Description">
                    <input type="date" name="due_date" placeholder="Due date">
                    <button type="submit">Add</button>
                </form>
            </details>
        </div>
    <?php endforeach; ?>
</div>
