<?php

declare(strict_types=1);

session_start();

define('SCORES_FILE', __DIR__ . '/scores.json');
define('DIFFICULTIES', ['easy' => 10, 'medium' => 15, 'hard' => 20]);
define('QUESTIONS_PER_QUIZ', 10);

// ---------- Helpers ----------

function loadScores(): array
{
    if (!file_exists(SCORES_FILE)) {
        return [];
    }
    $data = file_get_contents(SCORES_FILE);
    return is_array($decoded = json_decode($data, true)) ? $decoded : [];
}

function saveScores(array $scores): void
{
    file_put_contents(
        SCORES_FILE,
        json_encode($scores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function generateQuestion(string $difficulty): array
{
    $max = DIFFICULTIES[$difficulty];
    $ops = ['+', '-', '*'];
    if ($difficulty === 'hard') {
        $ops[] = '/';
    }
    $op = $ops[array_rand($ops)];
    $a = rand(1, $max);
    $b = rand(1, $max);

    // Ensure no negative results for subtraction
    if ($op === '-' && $a < $b) {
        [$a, $b] = [$b, $a];
    }
    // Ensure clean division
    if ($op === '/') {
        $b = rand(1, min(10, $max));
        $a = $b * rand(1, min(10, intdiv($max, $b) ?: 1));
    }

    // Compute answer
    $answer = match ($op) {
        '+' => $a + $b,
        '-' => $a - $b,
        '*' => $a * $b,
        '/' => intdiv($a, $b),
        default => 0,
    };

    return [
        'a'      => $a,
        'b'      => $b,
        'op'     => $op,
        'answer' => $answer,
    ];
}

// ---------- Quiz logic ----------

$difficulty = $_POST['difficulty'] ?? $_GET['difficulty'] ?? '';

if ($difficulty && !isset(DIFFICULTIES[$difficulty])) {
    $difficulty = 'easy';
}

// Initialize or reset quiz
$action = $_POST['action'] ?? '';

if ($action === 'start' && $difficulty) {
    $_SESSION['quiz'] = [
        'difficulty'  => $difficulty,
        'questions'   => [],
        'index'       => 0,
        'correct'     => 0,
        'start_time'  => time(),
        'streak'      => 0,
        'best_streak' => 0,
    ];
    for ($i = 0; $i < QUESTIONS_PER_QUIZ; $i++) {
        $_SESSION['quiz']['questions'][] = generateQuestion($difficulty);
    }
    unset($_POST['answer'], $_POST['question_index']);
}

// Process answer
$feedback = null;
$correctAnswer = null;

if ($action === 'answer' && isset($_SESSION['quiz'])) {
    $quiz = &$_SESSION['quiz'];
    $qIndex = (int) ($_POST['question_index'] ?? -1);
    $userAnswer = trim($_POST['answer'] ?? '');

    if ($qIndex >= 0 && $qIndex < QUESTIONS_PER_QUIZ && $qIndex === $quiz['index']) {
        $question = $quiz['questions'][$qIndex];
        $correctAnswer = $question['answer'];

        if (is_numeric($userAnswer) && (int) $userAnswer === $correctAnswer) {
            $quiz['correct']++;
            $quiz['streak']++;
            if ($quiz['streak'] > $quiz['best_streak']) {
                $quiz['best_streak'] = $quiz['streak'];
            }
            $feedback = 'correct';
        } else {
            $quiz['streak'] = 0;
            $feedback = 'wrong';
        }
        $quiz['index']++;
    }

    // Check if quiz is done
    if ($quiz['index'] >= QUESTIONS_PER_QUIZ) {
        $elapsed = time() - $quiz['start_time'];
        $score = [
            'score'       => $quiz['correct'],
            'total'       => QUESTIONS_PER_QUIZ,
            'difficulty'  => $quiz['difficulty'],
            'time'        => $elapsed,
            'best_streak' => $quiz['best_streak'],
            'date'        => date('Y-m-d H:i:s'),
        ];

        // Save high score
        $scores = loadScores();
        $scores[] = $score;
        saveScores($scores);

        $_SESSION['last_score'] = $score;
        $_SESSION['quiz'] = null;
        header('Location: result.php');
        exit;
    }
}

$quizActive = isset($_SESSION['quiz']) && $_SESSION['quiz'] !== null;

// Get current question data for display
$currentQuestion = null;
$progress = 0;
if ($quizActive) {
    $quiz = $_SESSION['quiz'];
    $currentQuestion = $quiz['questions'][$quiz['index']];
    $progress = $quiz['index'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🧮 Math Quiz</h1>

        <?php if (!$quizActive): ?>
            <!-- Difficulty selection -->
            <div class="card start-card">
                <h2>Choose Difficulty</h2>
                <form method="post">
                    <div class="difficulty-options">
                        <label class="diff-option">
                            <input type="radio" name="difficulty" value="easy" checked>
                            <span class="diff-label">Easy <small>(1–10)</small></span>
                        </label>
                        <label class="diff-option">
                            <input type="radio" name="difficulty" value="medium">
                            <span class="diff-label">Medium <small>(1–15)</small></span>
                        </label>
                        <label class="diff-option">
                            <input type="radio" name="difficulty" value="hard">
                            <span class="diff-label">Hard <small>(1–20, includes ÷)</small></span>
                        </label>
                    </div>
                    <button type="submit" name="action" value="start">Start Quiz</button>
                </form>

                <h3 style="margin-top: 32px;">🏆 High Scores</h3>
                <?php
                $scores = loadScores();
                if (empty($scores)):
                ?>
                    <p class="empty">No scores yet. Be the first!</p>
                <?php else:
                    // Show top 5
                    usort($scores, fn(array $a, array $b) => $b['score'] <=> $a['score']);
                    $top = array_slice($scores, 0, 5);
                ?>
                    <table class="scores-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Score</th>
                                <th>Difficulty</th>
                                <th>Time</th>
                                <th>Streak</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top as $i => $s): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= $s['score'] ?>/<?= $s['total'] ?></td>
                                    <td><?= ucfirst($s['difficulty']) ?></td>
                                    <td><?= $s['time'] ?>s</td>
                                    <td><?= $s['best_streak'] ?></td>
                                    <td><?= htmlspecialchars($s['date'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Active quiz -->
            <div class="card quiz-card">
                <div class="quiz-header">
                    <span>Question <?= $progress + 1 ?> of <?= QUESTIONS_PER_QUIZ ?></span>
                    <span>✅ <?= $_SESSION['quiz']['correct'] ?></span>
                    <span>🔥 Streak: <?= $_SESSION['quiz']['streak'] ?></span>
                </div>

                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= ($progress / QUESTIONS_PER_QUIZ) * 100 ?>%"></div>
                </div>

                <div class="question">
                    <span class="question-text">
                        <?= $currentQuestion['a'] ?>
                        <?= $currentQuestion['op'] ?>
                        <?= $currentQuestion['b'] ?>
                        = ?
                    </span>
                </div>

                <?php if ($feedback): ?>
                    <div class="feedback <?= $feedback ?>">
                        <?php if ($feedback === 'correct'): ?>
                            ✅ Correct!
                        <?php else: ?>
                            ❌ Wrong! The answer was <strong><?= $correctAnswer ?></strong>.
                        <?php endif; ?>
                    </div>
                    <?php if ($quizActive): ?>
                        <form method="post" class="next-form">
                            <input type="hidden" name="action" value="answer">
                            <input type="hidden" name="question_index" value="<?= $progress ?>">
                            <input type="hidden" name="answer" value="">
                            <button type="submit">Next &rarr;</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="post" class="answer-form">
                        <input type="hidden" name="action" value="answer">
                        <input type="hidden" name="question_index" value="<?= $progress ?>">
                        <input type="number" name="answer" id="answer-input" placeholder="Your answer" autofocus required>
                        <button type="submit">Submit</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
        // Auto-focus answer input when available
        const input = document.getElementById('answer-input');
        if (input) input.focus();
    </script>
</body>
</html>
