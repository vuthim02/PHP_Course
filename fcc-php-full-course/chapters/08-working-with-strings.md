# Chapter 08 — Working With Strings

**Video section 8 (44:27)**

## What the video teaches

Mike gives a broad overview of working with **strings** — one of the most common data types. Strings are just plain text, and PHP comes with **functions** — "little snippets of code that we can call" — that give you information about a string or modify it. He runs through the major ones one at a time.

### The basics: printing and storing a string

```php
<?php
echo "Giraffe Academy";
?>
```

- A string is plain text — anything you want to represent or use.
- Whenever you create a string you always make open and close quotation marks, then type anything inside.
- (Note: in the auto-captions this sounds like "Draft Academy"; the canonical text in the video is **"Giraffe Academy"**.)

You can also **store a string in a variable** — a container that makes the string easier to manage:

```php
<?php
$phrase = "Giraffe Academy";
echo $phrase;
?>
```

Now instead of printing the actual string you print the variable, and PHP prints the value stored inside it. You still get "Giraffe Academy" on the page.

### `strtolower()` — convert to lowercase

```php
<?php
echo strtolower($phrase);   // giraffe academy
?>
```

- "Str to lower" takes the string and converts it **entirely into lowercase**.

### `strtoupper()` — convert to uppercase

```php
<?php
echo strtoupper($phrase);   // GIRAFFE ACADEMY
echo strtoupper("dog");     // DOG
?>
```

- Converts the string **entirely into uppercase**.
- "You don't just have to pass the variable in here — I could pass anything I wanted in here." Passing `"dog"` prints `DOG`.

### `strlen()` — string length

```php
<?php
echo strlen($phrase);   // 15
?>
```

- Stands for **string length** — "this will tell us how many characters are in that string."
- `"Giraffe Academy"` has **15 characters**, and that's what prints.

### Indexing — pulling out individual characters

Strings are **indexed from zero**. "Whenever we have a string like this in PHP, we're going to start the indexing off at zero." For `"Giraffe Academy"`:

- `G` is index **0**, `i` is index **1**, `r` is **2**, `a` is **3**, `f` is **4**, `f` is **5**, `e` is **6**, the space is **7**, `A` is **8** ... and so on.

To grab one character, use **square brackets with an index** right after the string:

```php
<?php
echo $phrase[0];   // G  — the first character
echo $phrase[1];   // i  — the second character
echo "Mike"[0];    // M  — works on a literal string too
?>
```

- `$phrase[0]` prints **G** (the first character), because indexing starts at zero.
- `$phrase[1]` prints **i** (the second character).
- You can do this with things other than variables too — printing `"Mike"[0]` gives the capital **M**.

### Modifying a single character

You can also **modify an individual index** in a string:

```php
<?php
$phrase[0] = "B";
echo $phrase;   // Biraffe Academy
?>
```

- `$phrase[0] = "B"` replaces the character at index 0 with `B`.
- Now printing `$phrase` gives **"Biraffe Academy"** instead of "Giraffe Academy".
- "You can actually modify individual characters inside of your string."

### `str_replace()` — replace a substring

```php
<?php
echo str_replace("Giraffe", "Panda", $phrase);   // Panda Academy
?>
```

- "Str underscore replace" stands for **string replace**.
- It takes three arguments, in this order:
  1. The **substring you want to replace** — `"Giraffe"`.
  2. **What you want to replace it with** — `"Panda"`.
  3. **The string where you want to do this** — the variable `$phrase`.
- It replaces every occurrence of the first substring with the second inside the third string. Result: **"Panda Academy"**.
- "You could really do this with anything" — you could replace any character or sequence of characters with another sequence.

### `substr()` — grab a substring

```php
<?php
echo substr($phrase, 8);        // Academy
echo substr($phrase, 8, 3);     // Aca
?>
```

- `substr` lets you "just grab like a section of this overall string" — a single word, or a couple of characters.
- First argument: the string to grab from.
- Second argument: the **starting index**. To grab "Academy", count the indexes (`0,1,2,3,4,5,6,7,8...`) — Academy starts at index **8**, so `substr($phrase, 8)` grabs from index 8 to the end: **"Academy"**.
- Optional third argument: a **length** — how many characters to grab. `substr($phrase, 8, 3)` grabs 3 characters starting at index 8: **"Aca"**.

### There are many more string functions

- Mike is honest: "There's a lot more of these. I could spend a couple hours just going through each of the functions and what they do." This tutorial is meant to expose you to what string functions are and some ways to work with and modify strings.
- To find more: "You can basically just type into Google **PHP string functions**, and there'll be a huge list of all these awesome string functions that you can use."

## Key metaphor(s)

> Functions are "little snippets of code that we can call" — they either give us information about a string or modify it in some way.

Strings are **0-indexed**: the first character lives at position 0, the second at 1, and so on.

## Gotchas

- **Indexing starts at zero.** `G=0, i=1, r=2, a=3, f=4 ...` — the last character is at `length - 1`, not at `length`.
- **`str_replace` argument order matters:** (thing to find, thing to replace it with, the string to search). Mixing up the first two is a classic bug — it will silently do the opposite of what you want.
- **`substr` semantics:** second argument = start index, third (optional) = length of the slice, not an end index.
- **Modifying a character is permanent.** After `$phrase[0] = "B"`, the variable holds "Biraffe Academy". If you then run `str_replace("Giraffe", "Panda", $phrase)` against the same variable, there's no "Giraffe" left to find — that's why the video demonstrates the replace on the original "Giraffe Academy" string. Run replacements on the unmodified string (or a fresh copy) to get the "Panda Academy" output shown.
- Out-of-range index access gives you an undefined-offset warning in PHP 8. Always index within `0 .. strlen-1`.

## Modern note

PHP 8 added convenience functions that read like English:

```php
<?php
str_contains("Giraffe Academy", "Academy");  // true
str_starts_with("Giraffe Academy", "Giraffe"); // true
str_ends_with("Giraffe Academy", "y");         // true
?>
```

For non-ASCII text (accents, emoji, non-English alphabets), character counts and indexes change, so use the `mb_*` family instead: `mb_strlen`, `mb_substr`, `mb_strtoupper`, etc. Also note `str_replace` is **case-sensitive** (`str_ireplace` is the case-insensitive version).

## Checkpoint

1. What index holds the third character of a string, and why?
2. Write the argument order of `str_replace` from memory, then give an example.
3. Predict the output of `$phrase[0] = "B"; echo $phrase;` before running it.
4. What are the two arguments to `substr`? What does the third optional argument do?
5. Practice: take your own name, print it uppercase, its length, its first character, its last character, and replace one letter in it.

[← Chapter 07 — Data Types](07-data-types.md) | [Course Map](../README.md) | [Chapter 09 — Working With Numbers →](09-working-with-numbers.md)
