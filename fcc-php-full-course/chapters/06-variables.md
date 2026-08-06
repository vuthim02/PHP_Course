# Chapter 06 — Variables

**Video section 6 (27:30)**

## What the video teaches

Mike teaches variables from first principles — he builds up a real problem, shows why the naive solution is painful, then introduces variables as the fix. Watch the whole story build up; that's the lesson.

### The problem variables solve

Mike starts with a simple program that just echoes a little story:

```php
<?php
echo "There once was a man named George<br>";
echo "He was 70 years old<br>";
echo "He really liked the name George<br>";
echo "But didn't like being 70.<br>";
?>
```

On the browser it just prints the story. It's a valid program — simple, but it works.

Now suppose you want to **change the character's name**. You'd have to go into the story and manually change the name **every place it's mentioned** (say from George to John — two spots). And if you want to change the age (say from 70 to 35), you again manually change it everywhere it appears. With a four-line story, that's fine. **But imagine the story was hundreds of lines** and the name and age appear hundreds of times:

- "We're having to go in and manually change their name, or manually change their age, if I wanted to update it."
- It would be "extremely tedious and difficult."
- "I would probably make a mistake somewhere, you know, where I wouldn't catch it."

This is exactly the situation where you use a **variable**.

### What a variable is

> "A variable is basically just a container where we can store pieces of information in our program."

A lot of times in our programs we have certain pieces of information or data values that we want to keep track of and organize. Here there are two: the character's name and the character's age. So you create **two variables**, one for the name and one for the age, store the values in them, and then use the variables instead of typing the values over and over.

### Creating variables

Above the `echo` instructions, Mike creates the two variables:

```php
<?php
$characterName = "John";
$characterAge = 35;
?>
```

Line by line:

- **`$` (dollar sign):** "Anytime you want to create a variable in PHP, the first thing we have to do is type in this dollar sign. Whenever you type a dollar sign like that, it's basically telling PHP that you want to create a variable."
- **`characterName` / `characterAge`:** a descriptive name that tells you what piece of information is stored inside. ("A variable is the container where we're storing a piece of information," so name it descriptively.)
- **`=` :** assigns the value on the right to the variable.
- **The value:** "When I wanted to store text inside of a variable, I had to use these quotation marks" (`"John"`), "and when I wanted to store a number inside of a variable, I could just type out the number like that" (`35`). Different ways to store information.
- **`;` :** like every instruction, end with a semicolon.

### Using variables (interpolation inside strings)

Now replace every instance of the name and age inside the story with the variables. Inside a string, a `$variableName` gets replaced by the value of that variable:

```php
<?php
$characterName = "John";
$characterAge = 35;

echo "There once was a man named $characterName<br>";
echo "He was $characterAge years old<br>";
echo "He really liked the name $characterName<br>";
echo "But didn't like being $characterAge.<br>";
?>
```

When Mike types `$characterName` between the quotes, the editor highlights it in a different color. That's a visual clue for what's happening: "when we put this dollar sign and we type out the variable name here in this text... this is telling PHP that we want to insert the value that's stored inside of this variable into our little print statement here."

When you refresh the page, you still see "There once was a man named John..." and "He was 35 years old..." — **but you never physically typed "John" or "35"** into the echo lines. What PHP did: "it saw that we wanted to include the value of the characterName variable inside of here, and it went up, grabbed the value, and basically just inserted it here into our story."

### The payoff: change one spot, everything updates

Now to change the character's name, you edit **one place** — the variable assignment — instead of every mention:

```php
<?php
$characterName = "Tom";
$characterAge = 80;
?>
```

Refresh the page and the whole story now uses "Tom" and "80", automatically, everywhere. Same for the age. "That is a really awesome way that we can maintain and keep track of the different pieces of information in our programs. I can use this characterName variable, and then if I want to change the value, I just change it up here when I assign it a value."

### Variables can change midway through the program

Another cool thing: you can **modify variables throughout your program**. Let's say halfway through the story you want to change the character's name again:

```php
<?php
$characterName = "Tom";
$characterAge = 80;

echo "He really liked the name $characterName<br>";
echo "But didn't like being $characterAge.<br>";

$characterName = "Mike";

echo "He really liked the name $characterName<br>";
echo "But didn't like being $characterAge.<br>";
?>
```

- Before the reassignment, the story uses **Tom**.
- After `$characterName = "Mike";`, the rest of the story uses **Mike**.
- "Basically, you can just update these variables as you go through your program — you can change their values and do different things with them."

### Which things should be variables?

Not every piece of information needs to be a variable. Mike stores the values he **uses multiple times** (the name and the age) in variables, but not throwaway words like "once" or "man." The pattern: store what you keep referring to, and refer to the variable's **name** instead of physically typing the value each time.

## Key metaphor(s)

> "A variable is basically just a container where we can store pieces of information in our program."

Think of it as a labeled box: you write a name on the box (`$characterName`), put a value inside (`"John"`), and from then on you can point at the box instead of repeating the contents.

## Gotchas

- **The `$` is required** — `characterName = "John";` is a parse error; it must be `$characterName`.
- **Text needs quotes, numbers don't.** `"John"` vs `35`. Quoting a number (or not quoting text) is a classic bug.
- **Interpolation needs double quotes.** Inside a string, `"$characterName"` inserts the value — this is exactly how the video uses it.
- **Without variables you edit the same value in 100 places** and will almost certainly miss one or make a typo somewhere.
- Update the value in **one place** (the assignment) and every later use of the variable updates automatically.
- Variables remember their value until you **reassign** them; assignments later in the file override earlier ones.

## Modern note

- **Prefer single quotes when you don't need interpolation.** `'$characterName'` prints the literal text `$characterName` — it does *not* substitute. Use double quotes only when you want the value inserted (as in the video's story).
- For clarity in complex strings, PHP supports **brace interpolation**: `"name: {$characterName}"` — or use **concatenation**: `"name: " . $characterName`.
- PHP variables are **weakly typed** — a variable can hold a string and later a number without any declaration. (Chapters 7 and 29 will show the data types and how to add strict types.)

## Checkpoint

1. What is a variable, in your own words?
2. Why use variables instead of typing the values directly into the story?
3. Retype the full story example by hand, then change only the variable assignments to your own name/age and verify both update from one edit.
4. Predict the output of `'$characterAge years old'` with single quotes, then test it. What did PHP print, and why?
5. Add a line to the middle of the story that reassigns `$characterName`, and confirm the earlier lines still show the old name.

[← Chapter 05 — Writing HTML](05-writing-html.md) | [Course Map](../README.md) | [Chapter 07 — Data Types →](07-data-types.md)
