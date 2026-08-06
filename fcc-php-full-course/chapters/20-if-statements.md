# Chapter 20 — If Statements

**Video section 20 (2:19:10)**

## What the video teaches

An **if statement** is "basically a special programming structure, which allows our programs to make decisions." By using if statements, a program can *respond to* the different pieces of information inside it and do different things in different situations. "If statements are extremely useful, and basically they just make our programs a lot smarter."

### If statements exist in real life

Mike shows a little text file of everyday decisions — these are if statements you already use:

- **I wake up.** *If I'm hungry, I eat breakfast.*
- **I look at my phone.** *If it's about to die, I charge it.*
- **I leave my house.** *If it's cloudy, I bring an umbrella. Otherwise, I bring sunglasses.*

Each of these is a **condition** — "you're either hungry or you're not" — and the condition is either **true or false**:

- If the condition is **true** → perform the action ("eat breakfast", "charge it", "bring an umbrella").
- If the condition is **false** → "just move on" (no breakfast, no charging) — or, in the umbrella case, do the *otherwise* action ("bring sunglasses").

"That's essentially as complicated as if statements are: we're checking a condition, and if that condition is true, we're going to do something. And in some cases, if that condition is false, we could do something else."

### The basic if statement (with a Boolean variable)

First create a variable that tracks whether someone is male:

```php
<?php
$isMale = true;
?>
```

`$isMale` is a **Boolean variable** — it stores a Boolean value, i.e. a `true` or `false` value. Now write an if statement that responds to it:

```php
<?php
$isMale = true;

if ($isMale) {
    echo "You are male";
}
?>
```

- `if` — the keyword.
- `()` — inside the parentheses goes a **condition**, something that is either true or false.
- `{}` — inside the curly brackets goes the code to run when the condition is true.
- `$isMale` is a Boolean, so it can be used *directly* as the condition — "I'm checking to see if the person is male."

Run it with `$isMale = true;` → prints `You are male`. Change it to `false` → prints nothing, because the line between the curly brackets "is only going to get executed when this condition inside the parentheses is true." You can put as much code in there as you like — 20 or 30 lines if you want.

### The `else` keyword

What if, instead of printing nothing for a non-male, we want to *tell* them? That's what `else` is for:

```php
<?php
$isMale = false;

if ($isMale) {
    echo "You are male";
} else {
    echo "You are not male";
}
?>
```

- `else` — followed by `{}` — holds code that runs **when the condition is false**.
- With `$isMale = false;` the program now prints `You are not male`.
- "My program is now smart enough to be able to respond to this variable. If that variable is true, my program can respond to it. If it's false, my program can also respond to it."

### A second condition and the `&&` (AND) operator

Add a second Boolean:

```php
<?php
$isMale = true;
$isTall = true;
?>
```

Now the program deals with **two** pieces of information. Say we want to react when the person is *both* male *and* tall:

```php
<?php
$isMale = true;
$isTall = true;

if ($isMale && $isTall) {
    echo "You are a tall male";
} else {
    echo "You are not male";
}
?>
```

- `&&` is the **AND operator** — "it's just two ampersands."
- It lets you check a second condition inside the same `if`.
- For the whole condition to be true, **both** must be true: "if the person is male, *and* they're tall, then we're going to execute this code down here. But if they're not male, or they're not tall, then we're going to execute this down here."
- So if `$isTall = false;`, the whole thing becomes false and you fall into the `else` block — even though `$isMale` is still true. Same thing if *both* are false.

### The `||` (OR) operator

```php
<?php
$isMale = true;
$isTall = false;

if ($isMale || $isTall) {
    echo "You are a tall male";
}
?>
```

- `||` is the **OR operator** — "two vertical bars."
- It works like `&&` but with a key difference: "**only one of these conditions needs to be true** in order for the whole thing to be true."
- Here `$isMale` is true, so the block runs even though `$isTall` is false.
- "That's sort of the difference between and and or — essentially [both] just allow us to check multiple conditions."

### `elseif` and the `!` (negation) operator

Back to `&&`. We can now distinguish *every* combination of the two Booleans using `elseif` — "an else if is basically a way for me to check another condition, if this condition up here is false":

```php
<?php
$isMale = true;
$isTall = true;

if ($isMale && $isTall) {
    echo "You are a tall male";
} elseif ($isMale && !$isTall) {
    echo "You are a short male";
} elseif (!$isMale && $isTall) {
    echo "You are not male but are tall";
} else {
    echo "You are not male and not tall";
}
?>
```

Walk through the pieces:

- `elseif ($isMale && !$isTall)` — if the person **is** male but **is not** tall → "You are a short male".
- `!` is the **negation operator** (an exclamation point). It "takes the opposite of the condition that we specified": true → false, false → true. So `!$isTall` reads as *"is not tall"*.
- `elseif (!$isMale && $isTall)` — the flip side: **not** male but **is** tall → "You are not male but are tall".
- `else` — when none of the above matched (not male **and** not tall) → "You are not male and not tall".

This is a complete if/elseif/else chain. "Basically we're covering every possible situation":

| `$isMale` | `$isTall` | Message printed |
|---|---|---|
| `true` | `true` | You are a tall male |
| `true` | `false` | You are a short male |
| `false` | `true` | You are not male but are tall |
| `false` | `false` | You are not male and not tall |

"Using these if statements, my program was able to respond to the different pieces of information that it was given."

### Preview

There's more to if statements: **comparisons** — instead of only using Boolean variables, you can compare numbers and strings and use the result of the comparison as the condition. That's the next tutorial.

## Key metaphor(s)

> If statements make "our programs a lot smarter" by letting them respond to the data inside them.

> Real life is full of if statements: "I wake up. If I'm hungry, I eat breakfast." A condition is just something that is either true or false.

## Gotchas

- With **`&&`**, *both* sides must be true or the whole condition is false — one `false` ruins the whole thing.
- With **`||`**, only *one* side needs to be true.
- **`!`** flips a Boolean: `!$isTall` is true exactly when `$isTall` is false.
- `elseif` always comes *before* the final `else`. `else` runs only when *no* earlier condition matched.
- A Boolean variable can be used directly as a condition — no comparison needed.

## Modern note

- Write `elseif` as **one word** — that's the standard form (the two-word `else if` is only valid in braces in some contexts; one word is always safe).
- PHP also has the word operators `and` / `or`, but they have **lower precedence** than `&&` / `||`. Prefer `&&` and `||` for predictable results in condition chains.
- In PHP 8 you can write short arrow-style `fn` or `match` for some of these decisions (covered in chapter 23's modern note), but the if/elseif/else chain remains the fundamental tool.

## Checkpoint

1. Write the three operators covered in this chapter (`&&`, `||`, `!`) and say what each one requires.
2. Predict the output with `$isMale = false; $isTall = true;`, then test it.
3. What happens to the output if `$isTall` becomes `false` in the `&&` version, even though `$isMale` stays `true`?
4. Write an if/elseif/else that prints a message based on a person's age category (child / teen / adult).
5. Rewrite the isMale/isTall example using `||` and describe when each branch runs.

[← Chapter 19 — Return Statements](19-return-statements.md) | [Course Map](../README.md) | [Chapter 21 — If Statements (continued) — Comparisons →](21-if-statements-cont.md)
