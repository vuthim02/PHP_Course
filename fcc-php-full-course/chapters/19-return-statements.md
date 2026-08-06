# Chapter 19 — Return Statements

**Video section 19 (2:12:10)**

## What the video teaches

In the last tutorial, we gave functions information in the form of **parameters**. This tutorial is about the other direction: **a function can also give information back to the caller.** "Not only can I give that function information, but that function can also give information back to me." It can tell you how the function went, hand you a particular piece of information, an array, or a variable. The keyword that makes this possible is `return`.

### What "cubing" means

Mike starts by picking a task: cube a number. **Cubing a number means taking it to the power of three** — `2 raised to the power of 3` is the same as `2 * 2 * 2`. We're going to write a function you can pass a number into, and it will cube that number *and return the result* so the caller can use it.

### The cube function

```php
<?php
function cube($num) {
    return $num * $num * $num;
}
?>
```

- `function` — the keyword that defines the function.
- `cube` — the name, chosen because that's what it does.
- `()` + `{}` — parentheses for parameters, curly brackets for the body.
- `$num` — one parameter; the number we want to cube.
- `return $num * $num * $num;` — computes the cube (`$num` times itself three times) and hands the result back to whoever called the function.

The important part: "I don't just want to cube the number, I want to **return** the result of cubing the number back to the caller." `return` returns the value that follows it — "back to the caller, in other words, back to whoever called this function."

### Capturing the return value in a variable

```php
<?php
function cube($num) {
    return $num * $num * $num;
}

$cubeResult = cube(4);
echo $cubeResult;   // 64
?>
```

- `cube(4)` calls the function with `4`.
- PHP executes the body; when it hits `return`, it passes `4 * 4 * 4` back down to the call site.
- `$cubeResult = cube(4);` stores that returned value (64) inside the variable.
- `echo $cubeResult;` prints it.

Why 64? Because `4 * 4 = 16`, and `16 * 4 = 64` — so `4 cubed = 64`.

### The "no return" version

If you *remove* the `return` keyword and just leave `$num * $num * $num;` sitting in the function body, the function returns **nothing**. When you run the program, you get nothing printed — "that's because nothing was returned from this function." The `return` keyword is what hands the value out.

### Cutting out the middleman

You don't need the intermediate variable at all:

```php
<?php
function cube($num) {
    return $num * $num * $num;
}

echo cube(4);   // 64
?>
```

`echo cube(4);` prints the value directly. It "does the same thing, because this is getting a value back when we call that function."

### `return` is always the last thing that runs

```php
<?php
function cube($num) {
    return $num * $num * $num;
    echo "Hello";   // NEVER runs
}

echo cube(4);   // 64 (and "Hello" is never printed)
?>
```

"Whenever we put this return keyword in there, it's always going to be the last line of the function." When PHP executes the function and sees `return`, it **breaks out of the function** and jumps back to wherever it was called from. Anything after the `return` "never gets executed — PHP never sees it, never touches it." It's like saying: *"Hey, I'm done with this function."*

If you move the `echo "Hello";` line **above** the `return`, it runs fine — because it executes *before* the `return` breaks out of the function.

### You can return any type

"Really, you can return any type of value that you want":

- numbers (as shown),
- strings,
- arrays,
- associative arrays,
- basically any type of information.

### Returning nothing

```php
<?php
function myFunction() {
    // do some work...
    return;   // just break out, no value
}
?>
```

`return;` with no value still breaks you out of the function, it just doesn't return a value. People do this when they just want to exit a function early. "But I'd say for the most part, you're going to be returning information back to the caller."

## Key metaphor(s)

> `return` hands the computed value "back to the caller" — the function calls go off, run their code, and pass the result back down to where they were called.

> "Whenever I say `return`, that's basically me saying, 'Hey, I'm done with this function.'"

## Gotchas

- **Without `return`, a function returns nothing.** The computation happens, but no value is handed back, so nothing is echoed.
- **Code after `return` is dead code** — it never executes, because `return` breaks out of the function immediately.
- Even a bare `return;` (no value) still **exits the function** — you can't run anything after it either.
- You can return **any** type (number, string, array, associative array), not just numbers.

## Modern note

Modern PHP adds **types** and a power operator that make the cube function clearer:

```php
<?php
function cube(int $num): int {
    return $num ** 3;   // ** is the modern power operator
}

echo cube(4);   // 64
?>
```

- `int $num` declares the parameter type, `: int` declares the return type.
- `$num ** 3` is the exponent operator — literally "to the power of three".
- A one-line function can become an **arrow function** (PHP 7.4+):

```php
<?php
$cube = fn($num) => $num ** 3;
echo $cube(4);   // 64
?>
```

## Checkpoint

1. What does `return` do, and where must it effectively appear inside a function?
2. Why does `echo cube(4)` print 64 without ever storing the value in a variable?
3. What happens to `echo "Hello";` placed after `return`, and why?
4. Write a function `milesToFeet($miles)` that returns `$miles * 5280`, then print the result for 2 miles.
5. Can a function return a string or an array? What about a bare `return;`?

[← Chapter 18 — Functions](18-functions.md) | [Course Map](../README.md) | [Chapter 20 — If Statements →](20-if-statements.md)
