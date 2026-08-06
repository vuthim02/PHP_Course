# Chapter 07 — Data Types

**Video section 7 (38:09)**

## What the video teaches

Mike walks through the different **types of data** you can represent and work with in PHP. "As we go forward in the course, when we start writing more and more complex programs, we're going to be dealing with all different types of information and data." The setup: he creates a few variables and stores a different type of data in each one.

### 1. String — plain text

A string is **plain text** — a name, a location, a line in a book, anything that's plain text.

```php
<?php
$phrase = "To be or not to be";
?>
```

- Whenever you create a string, you always put **open and close quotation marks**.
- "Anytime you have the quotation marks there, PHP is going to know: Hey, this is going to be a string."
- Any text you want to store, work with, or represent in your program can be a string.

### 2. Integer — a whole number

PHP has **two basic types of numbers**. The first is an **integer** (an `int`), which is a **whole number** — counting numbers like 1, 2, 3, 4, 5 — essentially a number **without a decimal point**.

```php
<?php
$age = 30;
?>
```

- With numbers, "all you have to do is just type out the number" — **no quotation marks**.
- Numbers can be **negative** (for example, `-5`).
- There's no decimal point and no digits after one. That's what makes it an integer.

### 3. Float — a decimal number

The second type of number is a **floating point number** (also called a **float** or a **decimal number**). It's **any number with a decimal point** — for example, someone's GPA:

```php
<?php
$gpa = 3.7;
?>
```

It could also be `3.0` or `2.98743` — basically any decimal number you want to represent.

**Why the distinction matters:** "It's important to know the difference between decimal numbers and integers, because PHP is actually going to distinguish between them. There's a big difference in PHP between `30` and `30.0`" — `30.0` is a decimal, `30` is an integer.

### 4. Boolean — true or false

A **Boolean** is a **true or false value**. This is "probably a little bit less intuitive" than text and numbers, but a lot of the time when programming you need to represent true/false data.

```php
<?php
$isMale = true;
?>
```

- "Anytime we have a boolean variable, or anytime you're representing a boolean value, it can only be one of two things: either `true` or `false`."
- "It's like a **binary data type**."
- Booleans "are going to come in handy a lot in PHP" — the course will use them more later (spoiler: if statements!).

### 5. Null — no value

There's one more value you'll see sometimes in PHP: **null**.

```php
<?php
$nothing = null;
?>
```

- "Anytime you see `null` like this in PHP, this basically stands for **no value**."
- "A lot of times in PHP, we're going to kind of go out of our way to say something has no value."
- Sometimes you'll see an error message, or you'll try to print something out, and it'll basically just say `null` — because it has no value. "Anytime something has no value, PHP will denote that using this `null` keyword."

### The big picture

- These are really the **basic data types**. "As you go through your PHP journey, I'd say **99% of the time**, you're just going to be using these data types — text, numbers, or booleans."
- "With just this information, you can essentially build any program that you want."

### You don't always need a variable

You don't have to store data in a variable to use it — you can echo values directly:

```php
<?php
echo "Hello";   // a string, printed directly
echo 4.57;      // a number, printed directly
echo $phrase;   // the value stored inside the $phrase variable
?>
```

Strings, numbers, and variables all print to the screen the same way.

## Key metaphor(s)

> A Boolean is "like a binary data type" — it can only ever be one of two things: `true` or `false`.

Think of it as a light switch: on or off, no in-between.

## Gotchas

- **Quotes = string; no quotes = number.** `"35"` is text; `35` is a number. They behave differently.
- **`30` and `30.0` are different in PHP** — integer vs float, and PHP distinguishes between them.
- **Integers have no decimal point; floats do.** A negative number is still an integer if it has no decimal point (`-5`).
- **Booleans are lowercase** `true` / `false` in PHP.
- `echo $nothing;` prints **nothing at all** (empty) — which is exactly what "no value" means when rendered to a string.

## Modern note

Modern PHP (8.x) keeps all five of these types and adds more: **arrays**, **objects**, **iterable**, **mixed**, and — since PHP 8.1 — **enums**. You can inspect the exact type and value of anything with `var_dump()`:

```php
<?php
var_dump($phrase);  // string(16) "To be or not to be"
var_dump($age);     // int(30)
var_dump($gpa);     // float(3.7)
var_dump($isMale);  // bool(true)
var_dump($nothing); // NULL
?>
```

PHP 8 also adds **typed properties** and **strict types** (covered in the OOP chapters), so you can declare that a variable must hold, say, an int. For the basics of this chapter, the five classic types are all you need.

## Checkpoint

1. Name the five data types from the video and give one example value of each.
2. What is the difference between `30` and `30.0` in PHP?
3. Which type can only ever hold one of two values — and what are they?
4. What does `null` mean?
5. Write one variable of each type, then `var_dump()` all five and read the output. Which line prints nothing?

[← Chapter 06 — Variables](06-variables.md) | [Course Map](../README.md) | [Chapter 08 — Working With Strings →](08-working-with-strings.md)
