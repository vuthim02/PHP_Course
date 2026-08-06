# Chapter 21 — If Statements (continued) — Comparisons

**Video section 21 (2:37:16)**

## What the video teaches

Inside an if statement, you don't have to rely only on Boolean variables — you can also **compare different pieces of information** (numbers, strings, other types) and use the comparison as the condition. "Depending on the result of that comparison, I could do different things." This is one of the coolest things about if statements: "it allows us to compare and work with all the different pieces of information in our programs."

### The built-in `max` function

PHP already ships a function that finds the bigger of two numbers:

```php
<?php
echo max(3, 6);   // 6
?>
```

"No matter what numbers I pass in here, this function will always be able to tell me which one is bigger." Mike's goal: write our own version of `max` from scratch — which is exactly what if statements + comparisons let us do.

### Writing our own `getMax` function

```php
<?php
function getMax($num1, $num2) {
    if ($num1 > $num2) {
        return $num1;
    } else {
        return $num2;
    }
}
?>
```

Line by line:

- `function getMax($num1, $num2)` — he avoids the name `max` because PHP's built-in already uses it ("I don't want to confuse myself"). Two parameters, just like the built-in.
- `if ($num1 > $num2)` — a **comparison**. "You might not think that this is a true or false value, but it actually is. `$num1` is either greater than `$num2`, or it's not — it's either true or it's false." The comparison **gets resolved down to a true or false value**, so it works as a condition.
- `return $num1;` — if the comparison is true, `$num1` is definitely the biggest, so return it. Remember from chapter 19: `return` also breaks out of the function.
- `else { return $num2; }` — otherwise, `$num2` must be the biggest, so return that instead.

Testing:

```php
<?php
echo getMax(3, 90);    // 90
echo getMax(300, 90);  // 300
?>
```

- Mid-demo Mike forgets a semicolon and gets an error — he fixes it, refreshes, and gets `90`. "It looks like I forgot to put a semicolon over here. That's my bad."
- Switching the numbers so the first one is biggest also works: `getMax(300, 90)` → `300`. "Looks like our getMax function is working... and that's all thanks to this comparison."

### Extending it to three numbers — "process of elimination"

```php
<?php
function getMax3($num1, $num2, $num3) {
    if ($num1 >= $num2 && $num1 >= $num3) {
        return $num1;
    } elseif ($num2 >= $num1 && $num2 >= $num3) {
        return $num2;
    } else {
        return $num3;
    }
}
?>
```

- `if ($num1 >= $num2 && $num1 >= $num3)` — check whether `$num1` is the biggest *first*. The `>=` is "greater than or equal to" (Mike notes you could use `>` too, but he uses `>=`). The `&&` (AND, chapter 20) means **both** comparisons must be true. If so, `$num1` is the biggest → return it.
- `elseif ($num2 >= $num1 && $num2 >= $num3)` — if `$num1` isn't the biggest, check `$num2`: is it `>=` both of the others? If yes, return `$num2`.
- `else { return $num3; }` — "This one's a lot easier, because if `$num1`'s not the biggest and `$num2`'s not the biggest, then we're only left with one option, which is `$num3` being the biggest." This is **process of elimination**.

Testing every scenario:

```php
<?php
echo getMax3(300, 900, 400);   // 900  (middle one is biggest)
echo getMax3(3000, 900, 400);  // 3000 (first one is biggest)
echo getMax3(3000, 900, 3000); // 3000 (a tie — still correct, thanks to >=)
?>
```

Mike walks through each test: making the middle one the biggest → `900`; making the first one biggest → `3000`; and even when **two numbers are equal** (`>=` handles the tie) it still returns `3000`. "We tested out all the different possibilities, and we were able to be successful."

### The comparison operators

These symbols are called **comparison operators**:

| Operator | Meaning |
|---|---|
| `>` | greater than |
| `<` | less than |
| `>=` | greater than or equal to |
| `<=` | less than or equal to |
| `==` | equal to (double equals) |
| `!=` | not equal to (exclamation point equals) |

- `$num1 == $num2` — "this is basically saying if num1 is equal to num2, that's going to be a double equals."
- `$num1 != $num2` — "this whole thing is going to be true if num1 is *not* equal to num2."

### Comparing strings and other types

Comparisons aren't just for numbers — "we could also check to see if two strings are equal... You can use basically all the data types inside of these comparisons." The video demonstrates the idea that `==` works on strings too.

### A complete decision example (combining the operators)

The operators combine to build real decisions. Here's a full decision example using comparisons with `&&` and `||` — the same style of logic the video builds, applied to "what should I do today?":

```php
<?php
$temp = 82;         // degrees Fahrenheit
$isRaining = false;

if ($temp > 90 && !$isRaining) {
    echo "It's blazing hot and dry — stay in the shade";
} elseif ($temp > 80 || $isRaining) {
    echo "It's warm enough to go outside — grab a drink";
} elseif ($temp <= 50) {
    echo "It's cold — bring a jacket";
} else {
    echo "It's a perfect temperature";
}
?>
```

Read it the way Mike reads conditions:

- `$temp > 90 && !$isRaining` — **both** must hold: hotter than 90 AND not raining.
- `$temp > 80 || $isRaining` — **either** is enough: hotter than 80, OR it's raining.
- `$temp <= 50` — less than or equal to 50.
- The final `else` catches everything that matched nothing above.

Every branch's condition "gets resolved down to a true or false value."

"You're going to be using comparisons all the time with if statements, so you want to make sure that you have a firm grasp on how to use them."

## Key metaphor(s)

> A comparison "gets resolved down to a true or false value" — `$num1 > $num2` is either true or false, exactly like a Boolean.

> The three-number version works by **process of elimination**: if `$num1` isn't the biggest and `$num2` isn't the biggest, only `$num3` is left.

## Gotchas

- **`==` is equality, `=` is assignment.** Writing `if ($num1 = $num2)` assigns instead of compares — a classic bug that silently does the wrong thing.
- A comparison always produces `true` or `false` — that's *why* it works as a condition.
- Use `>=`/`<=` when ties matter (as in `getMax3`), so equal numbers still return a correct winner.
- Semicolons matter — Mike's own `echo getMax(...)` demo failed because he forgot one.
- The built-in name `max` is taken; name your own function differently (`getMax`) to avoid confusion.

## Modern note

- Prefer **strict** comparison `===` / `!==` to avoid type-juggling surprises: `"0" == 0` is `true` in PHP (loose equality), but `"0" === 0` is `false` (strict: different types). Strict is almost always what you want in modern PHP.
- PHP has a built-in `max(...)` (and `min(...)`) that accepts any number of arguments: `max(300, 900, 400)` → `900`. Writing `getMax3` yourself is a great exercise, but in production you'd use the built-ins.
- Comparisons with `==` on numbers where one side is a string can trigger automatic type conversion; `===` avoids it entirely.

## Checkpoint

1. Write all six comparison operators from memory with what each means.
2. Step through `getMax3(300, 900, 400)` — which condition is checked first, and what makes the function return 900?
3. What is the difference between `==` and `=`? Give a buggy line and fix it.
4. Why does `getMax3(3000, 900, 3000)` return `3000` instead of failing on the tie?
5. Write your own `getMin` function for two numbers using comparisons, then test it.
6. Build a decision that prints one of three messages based on temperature using `>` and `<=`.

[← Chapter 20 — If Statements](20-if-statements.md) | [Course Map](../README.md) | [Chapter 22 — Building a Better Calculator →](22-building-a-better-calculator.md)
