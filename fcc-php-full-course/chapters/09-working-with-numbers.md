# Chapter 09 — Working With Numbers

**Video section 9 (54:50)**

## What the video teaches

Mike gives a full introduction to **numbers** — one of the most important data types in PHP — plus the built-in **math operations** and **math functions** PHP offers. "Numbers is probably the most common type of data that we're going to be working with in our programs."

### Printing numbers

You can just type a number straight into `echo` — no quotation marks, no special characters:

```php
<?php
echo 40;    // 40
echo -40;   // negative numbers are fine
echo 8.47;  // decimal numbers are fine too
?>
```

PHP handles **negative numbers** and **decimal numbers** just as easily as whole ones.

### Integers vs floats

PHP differentiates between two distinct types of numbers:

- **Integer:** a whole number with no decimal point — like `40`.
- **Floating point number (float / decimal):** a number with a decimal point — like `8.47`.

"Honestly, it's not going to affect anything that much. But just so you're aware, there are those two distinct types of numbers" (a distinction PHP actually tracks internally, as covered in chapter 7).

### Arithmetic operators

PHP can do math for you. Instead of echoing `5 + 9`, it echoes **the answer**:

```php
<?php
echo 5 + 9;      // 14 — PHP solved the equation
echo 5.7 * 9;    // 51.3 — multiplication works too
?>
```

The four basic operators:

- **`+`** addition
- **`-`** subtraction
- **`/`** division
- **`*`** multiplication (note the asterisk)

"PHP is basically able to do any math that we throw at it."

### The modulus operator `%`

```php
<?php
echo 10 % 3;    // 1
?>
```

- Read as **"10 mod 3"**.
- "The modulus operator is going to take 10, divide it by 3, and give us the **remainder**." Since 10 ÷ 3 = 3 with a remainder of 1, the output is **1**.
- "There will be certain circumstances where you want to find out the remainder of a division."

### Order of operations

PHP follows the normal math order of operations:

```php
<?php
echo 4 + 5 * 10;     // 54 — multiplication first: 5*10 = 50, + 4 = 54
echo (4 + 5) * 10;   // 90 — parentheses override: 4+5 = 9, * 10 = 90
?>
```

- In `4 + 5 * 10`, PHP multiplies first (`5 * 10 = 50`), then adds 4 → **54**.
- Wrap `4 + 5` in **parentheses** and PHP does the addition first, then multiplies by 10 → **90**.
- "This just follows normal order of operations rules you're familiar with... and it's the same thing."

### Storing a number in a variable

```php
<?php
$num = 10;
echo $num;    // 10
?>
```

Set the variable to any number, then echo it — PHP prints the value stored inside.

### Increment and decrement: `++` and `--`

A lot of the time you'll want to add 1 to (or subtract 1 from) a numeric variable:

```php
<?php
$num = 10;
echo $num;    // 10
$num++;       // adds 1 → 11
echo $num;    // 11
$num--;       // subtracts 1 → back to 10
echo $num;    // 10
?>
```

- **`$num++;`** ("plus plus") "is basically just going to add one on to num." After it, echoing `$num` gives **11** instead of 10.
- **`$num--;`** ("minus minus") subtracts one from the number. (Mike's narration says the final value is "nine" — with the code above the decrement takes 11 back down to **10**; the auto-captions garbled the exact narration. The point is `--` subtracts exactly one.)

### Shorthand assignment: `+=`, `-=`, `*=`, `/=`

You can add a number onto a variable. The long way:

```php
<?php
$num = 10;
$num = $num + 25;   // 35
echo $num;
?>
```

"Basically, here I'm saying that I want num to be equal to num plus 25." But there's a **shorthand** that does exactly the same thing:

```php
<?php
$num = 10;
$num += 25;   // 10 + 25 = 35
echo $num;    // 35
?>
```

- `$num += 25;` is identical to `$num = $num + 25;` → **35**.
- The same pattern works for the other operators: **`-=`**, **`/=`**, and **`*=`** ("multiplication equals would just be the same as saying times 25"). They're just shorthand.
- "All of that stuff can be pretty useful, and it can be pretty fun to just kind of play around with that."

### Math functions

Beyond the basic operators, PHP has **functions** for more complex math. "Functions are basically just like little snippets of code that we can call, which will perform a specific operation for us" (you'll learn to write your own later in the course). Here are the ones Mike demonstrates:

```php
<?php
echo abs(-100);    // 100 — absolute value
echo pow(2, 4);    // 16 — 2 raised to the 4th power
echo sqrt(144);    // 12 — square root
echo max(2, 10);   // 10 — the bigger of the two numbers
echo min(2, 10);   // 2 — the smaller of the two numbers
?>
```

- **`abs()`** — absolute value: "if the number's negative or positive, it's always just going to give you the value." `abs(-100)` → **100**.
- **`pow()`** — raise a number to a power: `pow(2, 4)` → **16** (two to the fourth power).
- **`sqrt()`** — square root: `sqrt(144)` → **12**. "That's an easy way to get a square root of a number."
- **`max()`** — "it's going to tell us which of these two numbers is bigger." `max(2, 10)` → **10**.
- **`min()`** — the opposite: "it'll tell me which number is smaller." `min(2, 10)` → **2**.

### Rounding: `round()`, `ceil()`, `floor()`

```php
<?php
echo round(3.2);    // 3 — standard rounding rules
echo round(3.7);    // 4 — rounds up
echo ceil(3.3);     // 4 — always rounds UP, no matter what
echo floor(3.9);    // 3 — always rounds DOWN, no matter what
?>
```

- **`round()`** — rounds "according to just like standard rounding rules": `round(3.2)` → **3**, `round(3.7)` → **4**.
- **`ceil()`** — the **ceiling function**: "no matter what decimal point is over here, it's always going to round it up." `ceil(3.3)` → **4**.
- **`floor()`** — "no matter what this will round it down." `floor(3.9)` → **3**.

### There are dozens more

- "To be honest with you, there are dozens and dozens of these math functions that are available in PHP — all sorts of things to do with logarithms, and you can do stuff with sine, cosine, tangent, all that stuff."
- Mike doesn't spend time on every single one. To find more: **"just go online and search 'PHP math functions' — there's a bunch of pages with full explanations on how to use all of these guys."**
- His parting point: "Working with numbers is extremely important, and numbers is probably the most common type of data that we're going to be working with in our programs. So you want to make sure that you have a solid understanding of how numbers work."

## Key metaphor(s)

- The **modulus operator** is the "remainder finder": divide and hand back what's left over.
- Functions are "little snippets of code that we can call" — one function, one specific operation, no need to write the math yourself.

## Gotchas

- **`%` gives the remainder, not the quotient.** `10 % 3` is 1, not 3.
- **`*` is the multiplication symbol** (the asterisk), not `x`.
- **Parentheses override order of operations** — `4 + 5 * 10` and `(4 + 5) * 10` give different answers (54 vs 90).
- **`++`/`--` change the variable itself** — don't expect the original value afterwards.
- **`ceil` always rounds up, `floor` always rounds down**, regardless of the decimal part — unlike `round`, which goes to the nearest whole number.
- Integers and floats are **distinct types** (chapter 7): `40` vs `40.0`.

## Modern note

- The **power operator `**`** is the modern spelling: `2 ** 4` equals `pow(2, 4)`.
- `floor()`/`ceil()` round toward −∞ / +∞ (watch out with negatives: `floor(-3.2)` is `-4`).
- **Floats are imprecise** for money and exact decimals (e.g. `0.1 + 0.2` is not exactly `0.3`). For money, prefer **integers** (cents) or the **BCMath** functions (`bcadd`, `bcmul`, ...).
- `round()` accepts extra arguments for decimal places and rounding mode — e.g. `round(3.14159, 2)` → `3.14`.

## Checkpoint

1. What does `10 % 3` return, and why?
2. Why does `4 + 5 * 10` give 54 but `(4 + 5) * 10` give 90?
3. Predict each of these, then verify with PHP: `abs(-100)`, `pow(2, 4)`, `sqrt(144)`, `max(2, 10)`, `min(2, 10)`, `round(3.7)`, `ceil(3.3)`, `floor(3.9)`.
4. What is the difference between `round`, `ceil`, and `floor`?
5. Write a program that starts with `$num = 10`, then uses `++`, `+=`, `pow()`, and `sqrt()` together — e.g. increment it, add 25, and print the square root of the result. Run it and check each step.

[← Chapter 08 — Working With Strings](08-working-with-strings.md) | [Course Map](../README.md) | [Chapter 10](10-getting-user-input.md)
