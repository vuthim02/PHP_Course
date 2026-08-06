# Chapter 24 — While Loops

**Video section 24 (3:05:09)**

## What the video teaches

A **while loop** is "basically just a programming structure, which allows us to **loop over a specified block of code while a certain condition is true**." Loops let us "just keep repeating something as long as a certain condition is true." This tutorial covers the basics of while loops and one variation — the **do-while loop**.

### The two parts of a while loop

1. **The loop condition** — inside the parentheses. It works just like an if-statement condition: "that condition is going to determine whether or not we should keep executing the code in between the curly brackets."
2. **The loop body** — the code inside the curly brackets that gets repeated.

### Counting to 5

```php
<?php
$index = 1;

while ($index <= 5) {
    echo "while loop: $index<br>";
    $index++;
}
?>
```

- `$index = 1;` — a simple variable storing the number 1. This is the **counter**.
- `while ($index <= 5)` — the loop condition: keep looping "as long as the value stored inside the index variable is less than or equal to five."
- `echo "while loop: $index<br>";` — prints the current value of `$index`, with a line break so the output is easy to read.
- `$index++;` — increments the counter. `$index++` is the same as `$index = $index + 1` (from chapter 6).

Output:

```
while loop: 1
while loop: 2
while loop: 3
while loop: 4
while loop: 5
```

### Why it prints 12345

Mike walks through the mechanics step by step:

1. **The first thing PHP does is check the condition** — before executing *any* of the loop body. With `$index = 1`, `1 <= 5` is true, so we enter the body.
2. Print `1`, then `$index++` makes `$index` equal `2`.
3. "Once we've executed all the code inside of this loop body, we're going to **jump all the way back up** and check this condition again." We check the condition *before every single iteration*.
4. Print `2`, increment to `3`... then `3`, `4`, `5`.
5. When `$index` is `5`, we print `5`, then `$index++` makes it `6`.
6. Jump back up, check: `6 <= 5` is **false** → "we're going to break out of the while loop and we'll be done."

"That's the basics of how this while loop works — we define a looping condition; as long as that condition is true, we're going to go through and execute the code inside of here."

### Infinite loops — a warning

An **infinite loop** is "a situation where the condition inside of these parentheses is never going to be false" — you forget to increment something, or a value just never changes, and the condition stays true forever.

Mike demonstrates: delete the `$index++;` line. Now `$index` is *always* 1, so `1 <= 5` is always true. Refresh the page and "it's just a bunch of ones... I could scroll down infinitely, and it's just going to keep being ones." The program is continually printing ones onto the HTML document.

"You don't want to let a loop like this run, because it could slow down your computer significantly." He closes the tab to terminate it. "Infinite loops happen to everybody. If something's not working correctly, it might be because an infinite loop is occurring." He adds the `$index++;` line back.

### The do-while loop

First, the experiment: set `$index = 6;`. With a regular `while`, the very first thing PHP does is check `6 <= 5` — which is false — so **the body never runs and nothing prints**. "We never ran this echo command."

The do-while loop reverses the order:

```php
<?php
$index = 6;

do {
    echo "do while loop: $index<br>";
    $index++;
} while ($index <= 5);
?>
```

Mike transforms the while loop into a do-while: the `while` line moves down below the closing curly bracket (with a semicolon at the end), and `do` goes in front of the opening bracket. Now the body runs **first**, then the condition is checked:

- Execute the body: print `6`, increment to `7`.
- Check `7 <= 5` — false — exit the loop.

Output:

```
do while loop: 6
```

"Even though I have index equal to six up here, and it's technically not going to pass this condition, we're still going to be able to print it out — because in a do-while loop, we're executing the code inside of the loop body **before** we check the condition."

### While vs do-while

- **`while`**: check the condition first, then execute the loop body. If the condition is false at the start, the body never runs.
- **`do-while`**: execute the loop body first, then check the condition. The body **always runs at least once**.

"You're going to be encountering while loops a lot more than you will do-while loops. Do-while loops are more for a specific circumstance, but you will find them out there in the world — if you see them, now you'll know the difference."

### An if statement inside the loop (even numbers)

The real power of while loops is doing *different* work on each pass. Combine the loop with an if statement (chapter 20) to print only even numbers:

```php
<?php
$index = 1;

while ($index <= 10) {
    if ($index % 2 == 0) {
        echo "even: $index<br>";
    }
    $index++;
}
?>
```

- Each iteration checks `$index % 2 == 0` — the `%` operator gives the **remainder** of a division (explained in detail in a later chapter). An even number divided by 2 leaves remainder 0, so the condition is true only for even values.
- Odd numbers fall through the `if` and just get incremented; evens get echoed.

Output:

```
even: 2
even: 4
even: 6
even: 8
even: 10
```

This is the "loop + condition inside the body" pattern that while loops are built for.

## Key metaphor(s)

> A while loop is like a **repeating gate**: the condition is the gatekeeper — while it says "true", you keep passing through the body, and every pass you jump back to the gate and ask again.

## Gotchas

- **Forgetting the increment (or any change to the loop variable) creates an infinite loop** — the condition never becomes false and the page floods with output. Close the browser tab (or Ctrl+C on the terminal) to stop it; it can slow down your computer.
- `while` checks the condition **before** the body; `do-while` checks it **after**, so a do-while body always runs at least once.
- The condition is re-checked at the *start of every iteration* — not just once.

## Modern note

The semantics are unchanged in modern PHP (8.x). Which loop to pick:

- **`while`** — when you don't know in advance how many iterations you need and you decide at the top of each pass.
- **`do-while`** — when the body must run at least once regardless (e.g. showing a menu before asking "quit?").
- **`for`** — when you know the count in advance (chapter 25).
- **`foreach`** — when you're going over an array's contents (idiomatic in modern PHP).

## Checkpoint

1. What makes a while loop stop? Which line in the counting example is responsible?
2. What would happen if you removed `$index++;` — and how would you stop it?
3. With `$index = 6;`, why does the `while` version print nothing but the `do-while` version prints `6`?
4. Print all even numbers from 2 to 20 using a while loop with an `if` inside.
5. Describe a situation where a do-while is genuinely the better choice than a while.

[← Chapter 23 — Switch Statements](23-switch-statements.md) | [Course Map](../README.md) | [Chapter 25 — For Loops →](25-for-loops.md)
