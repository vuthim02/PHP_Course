# Chapter 17 — Associative Arrays

**Video section 17 (1:57:22)**

## What the video teaches

An **associative array** is "a special type of array where not only we can store data values, but we can actually store what are called **key value pairs**." Unlike a normal array, where you can store numbers or text or a combination of both, an associative array stores a series of key-value pairs — "which would allow me to access that information differently."

"The easiest way to wrap your mind around it is just to see an example."

### The scenario — a school website

"Let's say that I was writing a website for a school, and for this website I wanted to be able to keep track of the different students in my class and the grades that they got on a particular test." That's a scenario where an associative array fits: "inside of this array, I'm going to be storing two pieces of information — the student's name, and the grade that they got on the test. And those two data values are sort of like linked together."

### Creating an associative array

It's created just like a normal array — `$`, a name, `=`, `array()` with parentheses — but the elements are key-value pairs joined with the `=>` (equal sign, greater than sign) operator:

```php
<?php
$grades = array("Jim" => "A+", "Pam" => "B-", "Oscar" => "C+");
?>
```

- `$grades` — the variable name (matches the school-grades scenario).
- `"Jim" => "A+"` — "unlike a normal element in an array, I'm storing a key, which is the student's name, and then I'm storing the value. In other words, I'm mapping a value to a specific student's name." Jim's the key, `A+` is the value — Jim got an A+ on the test ("let's say Jim's really smart").
- `"Pam" => "B-"` — Pam got a B-.
- `"Oscar" => "C+"` — Oscar got a C+.
- The pairs are separated with commas, "just like I would normal array elements."
- "So you'll notice I'm storing a key and then I'm mapping it to a particular value — I'm storing the student's name, and I'm mapping that name to a particular grade."

### Accessing elements by key

To access an element, put the **key** in the square brackets instead of an index number:

```php
<?php
echo $grades["Jim"];     // A+
echo $grades["Oscar"];   // C+
?>
```

- `$grades["Jim"]` — "this is actually going to tell me what grade Jim got on the test." Echoing it prints `A+`. Doing the same for Oscar prints `C+`.
- "So unlike a normal array, where we access elements using their index position, in an associative array we access elements using what's called a **key**. And the key is basically this value over here [the part before the `=>`]."
- So Jim, Pam, and Oscar are the **keys**; `A+`, `B-`, `C+` are the **values**. "We have a key, and it's mapped to a particular value. And when I want to access that value inside the associative array, I just pass in the key."

### The rules — unique keys, repeatable values

"You want all of the keys inside of your associative array to be **unique**. So if I came over here and I made this student also named Jim, well, when I tried to access Jim, like, it's unclear which one we're referring to. So you always want to make sure that these are unique, right? So I have different names for all these keys." But "the **values can be the same** however" — Pam could also get an A+, just like Jim, "and that's going to be no problem."

### Modifying a value by key

Just like with a normal array, a value can be changed by targeting its key:

```php
<?php
$grades["Jim"] = "F";    // let's say Jim fails the test
echo $grades["Jim"];     // F
?>
```

"Let's say now Jim fails the test. And over here, when we print this out, Jim's going to have a new value of `F`."

### Everything a normal array can do

"You can essentially do everything you do with a normal array" — including counting the number of key mappings:

```php
<?php
echo count($grades);    // 3
?>
```

`count($grades)` tells how many key-value mappings there are — three students, so three.

### Building a real little website — grade lookup

Now Mike uses the array to build a website: "what I want to do is I want to write a website where the user can enter in a name, and then we will basically print out what grade that user got on the test." He has a simple form set up — `action="site.php"`, `method="post"` — and adds a text input:

```html
<form action="site.php" method="post">
    <input type="text" name="student">
    <input type="submit">
</form>
```

```php
<?php
echo $grades[$_POST["student"]];
?>
```

- The text box is `name="student"` — "essentially, inside of this textbox, we're going to be typing in the student's name whose grade we want to figure out."
- `echo $grades[$_POST["student"]]` — "I'm grabbing the value that the user entered inside of that text box, and I'm accessing that element inside of the associative array." The submitted name becomes the key used to look up the grade.

### Testing the website

Refreshing shows the text box. Typing `Jim` and clicking Submit returns the grade Jim got on the test — `A+`. Typing `Pam` returns Pam's grade, and `Oscar` returns `C+`.

"This is basically a way that we could wire up getting user input with an associative array. And this is actually really useful. So you can see how storing the information like this in an associative array, where we have key value pairs, makes it really, really easy for us to access that information in the future."

## Key metaphor(s)

- An associative array is a **lookup table**: give it a name (key) and it hands back the grade (value).
- The `=>` reads as "maps to": `"Jim" => "A+"` — Jim maps to A+.

## Gotchas

- **Keys must be unique** — two `Jim` keys would be ambiguous. **Values can repeat** — two students may both have `A+`.
- Use the key, not a numeric index, to read a value: `$grades["Jim"]`, not `$grades[0]`.
- The key spelling must match exactly (case-sensitive).
- `count($grades)` counts the *pairs*, not anything else — it's still the number of elements.

## Modern note

- Use the shorthand form: `["Jim" => "A+", "Pam" => "B-", "Oscar" => "C+"]`.
- Guard lookups against missing keys — typing a name that isn't a key produces a warning and `null`. Combine `??` on both the input and the lookup:

```php
<?php
echo $grades[$_POST["student"] ?? ""] ?? "Not found";
?>
```

- `isset($grades["Jim"])` and `array_key_exists("Jim", $grades)` both check for a key (they differ when a value is `null`).
- Iterate over the pairs with `foreach ($grades as $student => $grade) { ... }` (chapter 25's modern note).

## Checkpoint

1. What symbol pairs a key with its value?
2. Why must keys be unique but values can repeat?
3. Build the grade lookup: an associative array of 3 students, a form, and the PHP to look up and print a grade.
4. What happens if you type a name that isn't a key? Predict, then test, then fix with `??`.

[← Chapter 16 — Using Checkboxes](16-using-checkboxes.md) | [Course Map](../README.md) | [Chapter 18 — Functions →](18-functions.md)
