# Chapter 15 — Arrays

**Video section 15 (1:41:44)**

## What the video teaches

An **array** is "basically a container or a structure where we can store multiple pieces of information." A lot of times in PHP we deal with all types of data, and one way we've been managing that data so far is with **variables**. A variable "is great because it can store one single value" — it's a container for a single data value.

"But a lot of times in PHP, we're not just going to want to be able to store one value — we're going to want to be able to store large groups of values." An array "is actually very similar to a variable but, unlike a variable, an array can store more than one piece of information inside of it." An array could hold 10, 20, 100, 1000, even a **million** values. "That's why arrays are really useful" — there are tons of situations where you want to store and keep track of large amounts of information.

### Creating an array

You create an array "very similar to the way that we create a normal variable" — `$`, a descriptive name that tells you what type of information is stored inside, `=`, then the values:

```php
<?php
$friends = array("Kevin", "Karen", "Oscar", "Jim");
?>
```

- `$friends` — the name; this array stores a bunch of names, e.g., a list of friends.
- `array( ... )` — the `array()` keyword with open and close parentheses is how the array is constructed.
- The statement ends with a semicolon, like any other.
- Inside the parentheses, multiple pieces of information can be stored: `"Kevin", "Karen", "Oscar", "Jim"`.
- Each value is separated with a comma. Each one is an **element** inside the `friends` array — "these are all elements inside of this one structure, inside of this one container."
- "Unlike a variable where I can only store one string, in this array I can store multiple strings side by side, just like that."
- You can store *any* type of data — "maybe in here I also wanted to throw in a number... or a boolean value. Really, you can put any type of information that you want inside of these arrays, it's not going to matter."

### Accessing elements (0-indexed)

"The information is no good if we can't access it." Echoing `$friends` alone just prints `Array` — "it's basically just telling us like, hey, this is an array, there's a bunch of stuff in here." To get an individual element, write the variable name followed by square brackets holding the **index**:

```php
<?php
echo $friends[0];    // Kevin
echo $friends[1];    // Karen
echo $friends[2];    // Oscar
?>
```

- "All of the elements inside of this array are assigned index positions. And all you have to do in order to access this specific element is put its index inside of these square brackets."
- `$friends[0]` → `Kevin` (the first element). `$friends[1]` → `Karen`. `$friends[2]` → `Oscar`.
- **Indexing starts at zero.** "The first element in the array, this Kevin, is actually at index position zero. And then the second element in the array, Karen, is at index position one, and so forth." So the positions are 0, 1, 2, 3 — "and this is very important."
- "If you're familiar with strings in PHP, this is actually the same way that we index strings. So we start with zero."

### Modifying an element

An element can be reassigned just like a variable, by targeting its index:

```php
<?php
$friends[1] = "Dwight";
echo $friends[1];    // Dwight
?>
```

- To modify the element at index 1 (Karen), write `$friends[1]` and give it a new value — `"Dwight"`. Now printing `$friends[1]` gives `Dwight` instead of `Karen`.
- "It's also important to note that we can store different data types in these arrays alongside each other" — e.g., putting `400` (a number, not a string) at some index still works, and you can print `400`.

### Adding an element at any index

There are four elements at indices 0–3, so there's nothing at index 4 — but you can add one:

```php
<?php
$friends[4] = "Angela";
echo $friends[4];    // Angela
?>
```

- `$friends[4] = "Angela"` adds another friend onto the end of the list.
- You could even use index `10` — "it's going to be no problem, it's going to be able to handle that just fine."

### Counting elements

A very useful trick: find out how many elements are inside the array with **`count()`**:

```php
<?php
echo count($friends);    // 4
?>
```

- `count` followed by parentheses wrapping the array — `count($friends)` — "this is going to tell me how many elements are inside of this array." With Kevin, Karen, Oscar, Jim it prints `4`.
- Add another element (`$friends[4] = "Mike";`) and now it prints `5` — "because I added another element."

"And that's kind of the basics of working with arrays. Arrays are extremely useful, and there's going to be tons of situations where you want to use them, so you want to make sure that you have a pretty solid understanding of these going forward."

## Key metaphor(s)

> An array is "very similar to a variable but, unlike a variable, an array can store more than one piece of information" — a container that can hold one piece of data *or* a million.

The elements are like numbered slots in a mailbox: slot 0, slot 1, slot 2...

## Gotchas

- Indexing starts at **zero** — the first element is `[0]`, not `[1]` (the same rule as indexing strings, chapter 8).
- Echoing an entire array just prints the word `Array` — use an index, `count()`, or (later) a loop to get at the values.
- Comma placement: every element except the last is followed by a comma.
- The index in `$friends[1] = "Dwight";` is the *position*, and reassignment overwrites whatever was there.
- Values don't all have to be the same type — numbers, booleans, and strings can sit side by side.

## Modern note

- Use the shorthand `[...]` instead of `array(...)` — it's identical but shorter and the modern convention:

```php
<?php
$friends = ["Kevin", "Karen", "Oscar", "Jim"];
?>
```

- Inspect arrays with `var_dump()` (type + length + values) or `print_r()` (human-readable).
- Add to the end cleanly with `$friends[] = "Angela";` (auto-increments the index) or `array_push($friends, "Angela");`.

## Checkpoint

1. What is the index of the third element of an array?
2. How do you find out how many elements an array has?
3. Create an array of 4 things you own, print the first and last, change one, and print `count()`.
4. What does `echo $friends;` print, and why?

[← Chapter 14 — POST vs GET](14-post-vs-get.md) | [Course Map](../README.md) | [Chapter 16 — Using Checkboxes →](16-using-checkboxes.md)
