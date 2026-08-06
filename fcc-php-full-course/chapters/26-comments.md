# Chapter 26 — Comments

**Video section 26 (3:26:24)**

## What the video teaches

A **comment** is "basically just a line of code inside of our PHP file, which **isn't going to get rendered by PHP**." When we write lines of code, they're instructions meant for the computer ("I'm telling PHP to do something"). But a lot of the time we want to leave little notes or reminders for ourselves or for other developers — and that's exactly what comments are for. "Any text inside of our PHP file that's not meant for the computer, it's meant for us humans."

### Single-line comments with `//`

```php
<?php
// This is a comment
echo "Hello";
?>
```

- All you have to do is type **two forward slashes**: `//`.
- Everything after the `//` on that line is a comment. You'll notice in the text editor that the comment is "colored a little bit different than some of the code" — because it's no longer an instruction for the computer, just plain text for the developer to read.

Ways people use single-line comments:

- **Leave little notes** — "you can leave like a little to-do stub here."
- **Describe a line of code** — e.g. `// This line prints out a string` right above the `echo`.
- **After a line of code** (inline comments):

```php
<?php
echo "Hello";   // This is also a comment
?>
```

"Anything that comes after these two forward slashes is going to be considered a comment."

**The catch:** `//` only works on a **single line**. "If I was to come down here and start typing, you'll see now this is no longer considered a comment" — only the stuff on the *same line* as the `//` is a comment.

### Multiple single-line comments

If you want to span several lines, one simple approach is to just put `//` on each line:

```php
<?php
// This is a comment on line one
// This is a comment on line two
// This is a comment on line three
?>
```

### Comment blocks with `/* */`

The cleaner way to make a multi-line comment is a **comment block** — a block where you can put as many lines of comments as you want:

```php
<?php
/*
This is a comment block.
It can span as many lines as I want.
Anything between the start and end tags is a comment.
*/
?>
```

- Start it with a **forward slash + asterisk**: `/*`.
- End it with an **asterisk + forward slash**: `*/`.
- "After I type this in, everything down here changed color — everything basically became a comment. That is until I make another asterisk and another forward slash." Only what's *between* the starting and ending tags is considered a comment.
- You can write on as many lines as you like — "this whole thing in between these comment blocks is going to be considered a comment."

### Commenting out a line of code

One very common developer use of comments: **commenting out** code.

"Sometimes you might have a line of code which you think is causing trouble — that line is breaking your program or something. A lot of times you're going to want to test your programs without those specific lines of code."

```php
<?php
// echo "This line might be causing problems";
echo "Hello";
?>
```

- Option one: physically delete the line, run the program. It works — but you've destroyed the code.
- Option two: **put a comment in front of it**. "This whole thing is a comment, and instead of having to delete the line of code, we get the same result where this line of code doesn't get executed — but without having to delete it."

So commenting out is a way to temporarily disable a line for testing or debugging, while keeping it in the file in case you need it back.

### Comments are completely open-ended

"A comment is extremely open-ended. It's just anything that's not going to get rendered by the computer. Any text that you want to put in there, any notes, you can write logs in there, you can do whatever you want with a comment."

## Key metaphor(s)

> Comments are **"for us humans, not the computer"** — the machine skips right past them; only people read them.

> Commenting out code is like **temporarily unplugging** a line instead of deleting it — same effect, nothing lost.

## Gotchas

- `//` comments out only the rest of **that one line** — the next line is normal code again.
- For multiple lines, use `/* ... */` (or several `//` lines).
- Don't forget the closing `*/` — everything after a lone `/*` stays commented out until PHP finds the end tag (or the file ends).
- A commented-out line still isn't executed — which is exactly the point when debugging, but it also means the code won't run if you forget it's disabled.

## Modern note

- Comment syntax is identical in modern PHP (8.x).
- **Docblocks** (`/** ... */`) are a special comment convention that IDEs, editors, and documentation tools understand:

```php
<?php
/**
 * Converts miles to feet.
 *
 * @param int $miles The number of miles.
 * @return int The number of feet.
 */
function milesToFeet(int $miles): int {
    return $miles * 5280;
}
?>
```

- Good modern practice: use comments to explain *why*, not *what* — let clear variable names and function names carry the "what" (self-documenting code).

## Checkpoint

1. What is the difference between `//` and `/* */`?
2. Why would you comment out a line of code instead of deleting it?
3. What happens if you write text after a `//` on one line, then keep typing on the next line — is it still a comment?
4. Take any program from an earlier chapter and add comments explaining each part.
5. Write a multi-line comment block that documents a function's purpose.

[← Chapter 25 — For Loops](25-for-loops.md) | [Course Map](../README.md) | [Chapter 27 — Including HTML →](27-including-html.md)
