# Chapter 23 — Switch Statements

**Video section 23 (2:56:53)**

## What the video teaches

A **switch statement** is "basically just a special type of if statement, which we can use to compare **one value to a bunch of different values**." There are times when you want if-statement functionality but you're checking many possible cases — in that situation a switch makes it "a lot easier." A switch is "another way that we can allow our program to respond to different information."

### The starting program: ask for a grade

Mike sets up a tiny program: an HTML form asking the user what grade they got on a test, storing it in a variable and printing it back.

```php
<?php
$grade = $_POST["grade"];
echo $grade;
?>
```

Very simple — if you type `A+`, click Submit, it prints `A+`. Now we'll upgrade it: instead of just echoing the grade, the program will **tell the user how they did**. If they got an A → "you did really well"; a B → "you did all right"; a C → "you could do better next time"; an F → you failed.

### The switch structure

```php
<?php
switch ($grade) {
    case "A":
        echo "You did amazing";
        break;
    case "B":
        echo "You did pretty good";
        break;
    case "C":
        echo "You did poorly";
        break;
    case "D":
        echo "You did very bad";
        break;
    case "F":
        echo "You fail";
        break;
    default:
        echo "Invalid Grade";
}
?>
```

- `switch ($grade)` — inside the parentheses goes the value we're comparing: "we're going to compare the grade to a bunch of different things."
- `case "A":` — "in the **case** that the grade is equal to a capital A, I can come down here and type out some code." Each case ends with a **colon**.
- `break;` — stops the switch (explained below).
- `default:` — runs when *none* of the cases matched.

### Building it up and testing

Mike starts with just the `A` case, then adds `B`:

- Type a capital `A`, click Submit → `You did amazing` (instead of echoing `A`).
- Type a `B` *before* a `B` case exists → **nothing prints** ("that's because I didn't tell it to do anything").
- Add the `B` case → `You did pretty good`.

Then he fills in cases for **all the common grades — A, B, C, D, F** — each with its own message:

| Case | Message |
|---|---|
| `"A"` | You did amazing |
| `"B"` | You did pretty good |
| `"C"` | You did poorly |
| `"D"` | You did very bad |
| `"F"` | You fail |

Now the program responds to lots of different grades: `F` → "you fail", `C` → "you did poorly", `A` → "you did amazing", `B` → "you did pretty good".

### What `break` does

"`break` is basically a statement that will **break us out of a programming structure**. Switch over here is a programming structure — we're in here between these open and close curly brackets, and when we put `break` here, it basically breaks us out of the switch statement."

Why it matters: say the grade is `A`, so the `A` case runs and prints "You did amazing". **Without** the `break`, the switch would *keep executing* — "even though I figured out that the grade was an A, I would keep looking through all of the other cases." The `break` says: "once we figured out that the grade was equal to A, I don't want to check any more cases." You don't *have* to put it there, "but a lot of times, people will, just because it's more useful."

### The `default` case

There's one problem: type an **invalid grade** (like `G`) and nothing prints. "No error is getting thrown — the program is still running, but nothing is actually getting printed out. And the problem is that we're not handling that situation."

The fix is `default:`:

```php
default:
    echo "Invalid Grade";
```

"When none of these cases up here are true — in other words, when it's not A, B, C, D, or F — then we're going to go ahead and execute this code." Now typing `G` prints `Invalid Grade`, while `A` still prints `You did amazing`.

### Switch vs if

"Everything that you can do with a switch statement, you could do with an if statement — it's just that switch statements make it a lot easier for us to do that." A switch is the right tool "in a situation where you have one value, like the grade, and you want to compare it to a bunch of different values."

## Key metaphor(s)

> A switch is a **menu of cases** for one value — compare that one value against each case until one matches (or hit `default`).

> `break` is the "I'm done here, stop checking the rest" statement.

## Gotchas

- **Forgetting `break` causes fall-through**: after a case matches, the switch keeps running the remaining cases even though you already found your match.
- **With no `default` and no match, nothing is printed** — no error, just silence.
- Each `case` ends with a **colon** (`case "A":`), not a semicolon.
- Switch compares loosely by default (`==`), so `case 0:` can accidentally match falsy values like `""` or `"0"` — be careful when switching on variables that may be empty.

## Modern note

The modern replacement is the **`match` expression** (PHP 8) — no `break` needed, usable as a value, and **strict** by default:

```php
<?php
$msg = match ($grade) {
    "A" => "You did amazing",
    "B" => "You did pretty good",
    "C" => "You did poorly",
    "D" => "You did very bad",
    "F" => "You fail",
    default => "Invalid Grade",
};
echo $msg;
?>
```

With `match`, a non-exhaustive match (no `default`) throws an `UnhandledMatchError` instead of silently printing nothing. The switch version from the video is still worth knowing — you'll see it in older code everywhere.

## Checkpoint

1. What does `break` do, and what happens to the output if you remove it from the `A` case?
2. When does the `default` case run, and what did the program print *before* `default` existed?
3. Build the grade form + switch and test every grade (`A`–`F`) plus an invalid one — what message does each produce?
4. Convert your switch to `match` and compare the behavior when no case matches.

[← Chapter 22 — Building a Better Calculator](22-building-a-better-calculator.md) | [Course Map](../README.md) | [Chapter 24 — While Loops →](24-while-loops.md)
