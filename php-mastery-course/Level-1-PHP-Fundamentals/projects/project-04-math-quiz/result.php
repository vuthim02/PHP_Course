<?php

declare(strict_types=1);

session_start();

$score = $_SESSION['last_score'] ?? null;

if (!$score) {
    header('Location: index.php');
    exit;
}

// Clear so refresh doesn't show stale data
$_SESSION['last_score'] = null;

$percent = $score['total'] > 0
    ? round(($score['score'] / $score['total']) * 100)
    : 0;

$grade = match (true) {
    $percent >= 90 => 'A+ 🎉',
    $percent >= 80 => 'A  👍',
    $percent >= 70 => 'B  💪',
    $percent >= 60 => 'C  👌',
    $percent >= 50 => 'D  😅',
    default        => 'F  💀',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>📊 Quiz Results</h1>

        <div class="card result-card">
            <div class="grade"><?= $grade ?></div>
            <div class="big-score"><?= $score['score'] ?> / <?= $score['total'] ?></div>
            <div class="percent"><?= $percent ?>%</div>

            <div class="stats">
                <div class="stat">
                    <span class="stat-label">Difficulty</span>
                    <span class="stat-value"><?= ucfirst($score['difficulty']) ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Time</span>
                    <span class="stat-value"><?= $score['time'] ?> seconds</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Best Streak</span>
                    <span class="stat-value"><?= $score['best_streak'] ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Avg per question</span>
                    <span class="stat-value"><?= $score['total'] > 0 ? round($score['time'] / $score['total'], 1) : 0 ?>s</span>
                </div>
            </div>

            <a href="index.php" class="btn-again">Play Again</a>
        </div>
    </div>
</body>
</html>
