# Project 4: Math Quiz App

A web-based math quiz that generates random arithmetic problems. Choose difficulty, answer 10 questions, and get scored with stats. High scores are saved and displayed on the start screen.

## Learning Objectives

- Random number generation (`rand`)
- Math operators in dynamic expressions
- Session management for quiz state
- Form handling with multi-step logic
- Array operations for question generation
- Arithmetic logic (including safe division)
- Date/time for timestamps
- Conditional rendering (start vs. quiz vs. result)
- Data persistence with JSON

## Features

- Three difficulties: Easy (1–10), Medium (1–15), Hard (1–20 with division)
- 10 random questions per quiz (+, -, \*, / depending on difficulty)
- Progress bar with streak tracking
- Correct/wrong feedback after each answer
- Results page with letter grade, percentage, time, best streak
- High scores table (top 5) on the start screen

## How to Run

```bash
php -S localhost:8000 -t project-04-math-quiz/
```

Then open `http://localhost:8000/index.php` in a browser.

## Code Structure

| File           | Purpose                                         |
|----------------|-------------------------------------------------|
| `index.php`    | Quiz engine — start screen, active quiz, logic  |
| `result.php`   | Displays score, stats, grade after quiz ends    |
| `scores.json`  | Auto-created high scores data file              |
| `style.css`    | Styling with dark theme                         |
| `README.md`    | This file                                       |

## PHP Concepts Practiced

| Concept                | Usage                                          |
|------------------------|------------------------------------------------|
| Math / numbers         | `rand`, arithmetic, `intdiv`, random operators |
| Sessions               | Quiz state across requests                     |
| Superglobals           | `$_POST`, `$_GET`, `$_SESSION`                 |
| Control flow           | `match`, `switch`, `if`/`else` for quiz logic  |
| Arrays                 | `array_rand`, `usort`, `array_slice`           |
| Functions              | `generateQuestion`, `loadScores`, `saveScores` |
| Date/time              | `time()` for elapsed time, `date()` for logging |
| Strings                | `ucfirst`, number formatting                   |
| JSON                   | `json_encode` / `json_decode`                  |
| File operations        | `file_get_contents`, `file_put_contents`       |
| Forms                  | POST with hidden inputs for state              |
| Error handling         | Guard clauses, isset checks, type safety       |
