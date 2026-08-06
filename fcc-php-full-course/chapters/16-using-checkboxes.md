# Chapter 16 — Using Checkboxes

**Video section 16 (1:50:26)**

## What the video teaches

"This is going to be a pretty cool tutorial, because not only are we going to learn how to get input from checkboxes, but we're also going to see how we can use arrays out in the real world." The example: get information from the user, store that information inside an array, and then work with it.

### The starting program

Mike already has a basic program set up: a form with `action="site.php"` (the file he's currently working on), `method="post"`, and a Submit button — a pretty standard form. Down in the PHP he hasn't written anything yet.

### What a checkbox is

A **checkbox** is "basically just a little box where you can check." Mike sets up a list of checkboxes, and the user gets to **select their favorite fruits** — check which fruits they like, then submit that information.

### Building the checkboxes

Right on top of the Submit button, Mike adds an input:

```html
<form action="site.php" method="post">
    Apples: <input type="checkbox" name="fruits[]" value="apples"><br>
    Oranges: <input type="checkbox" name="fruits[]" value="oranges"><br>
    Pears: <input type="checkbox" name="fruits[]" value="pears"><br>
    <input type="submit">
</form>
```

Step by step:
- `<input type="checkbox">` — tells HTML to create a checkbox.
- `name="fruits[]"` — "whenever we're trying to get input from multiple checkboxes, we always want to put these square brackets here. And basically, that's going to signify that we're going to store all of these fruits inside of an array. And once they're in the array, it'll be a lot easier for us to work with them and do different things with them."
- `value="apples"` — the **value** is "essentially going to be the value that this checkbox is going to have associated to it." This checkbox is for apples, so if the user checks it, that means they like apples — "because that's the value over here."
- To the *left* of the input he types what it's for: `Apples:`.
- A `<br>` follows, then the same line is copied for `oranges` and `pears` (both the label and the `value` change).
- "Making the checkboxes is pretty simple. Again, we just have to specify the type, we have to give it a name. And remember, if we want all of these checkboxes to sort of be stored inside of the same array — in other words, if we want the values that the user checks to be stored in the same container — we have to name it just like this [with the same `fruits[]` name]. And then finally, we give each of these a value."

Refreshing the page shows three checkboxes. "What's cool about checkboxes is I can check multiple boxes... I can basically just check and uncheck as many as I want."

### The question — getting the checked values

"Here's the question though: what I want to do is I want to be able to get the values that the user checks." If the user checks apples and oranges, when they click Submit, that information should be retrievable. If they select all three, all three should come through.

"Because the user is able to select multiple pieces of information here, we're storing it inside of an array. And remember, an array is just a container that can hold multiple pieces of information. And that's basically what I was saying down here in this name: I'm saying I want to store the values that the user checks inside of this `fruits` array."

### Reading them in PHP

```php
<?php
$fruits = $_POST["fruits"];

echo $fruits[0];    // first fruit that was checked
echo $fruits[1];    // second fruit that was checked
?>
```

- `$fruits = $_POST["fruits"];` — `$_POST` because the form's method is `post`. The name is typed in without the square brackets — "we don't need that" in the PHP lookup.
- "Down here, I now am storing all the fruits that the user checked and submitted inside of this variable. And actually, this is an array — an array that's holding all the fruits that the user checked from the checkbox."
- `echo $fruits[0]` — "this is basically going to tell us what the first fruit that was checked is."
- `echo $fruits[1]` — the second fruit checked.

### Testing it

- Refresh the page, check **apples** and **oranges**, click Submit → the page prints `apples` — "because that was the first checkbox that I checked."
- `$fruits[1]` → `oranges`, "because that was the second element that was stored inside of that array."
- Check only **one** box → `$fruits[1]` is blank, "since I'm trying to print out the second element in the array" and there isn't one.
- Check **oranges** and **pears** → `$fruits[1]` prints `pears` (the second element in the submitted array).

### Summary

"So that's basically how these checkboxes work, right? I can set up all these different checkboxes, and because I gave them all the same name over here, they're all going to be stored inside of the same array. So when I click that Submit button, all that information is getting passed back to PHP on the server, and it's basically storing that information inside of an array, and then I can work with that information and do different things with it. So this is a pretty useful thing to do on your websites. And as you can see, it's very simple, right? And we can use that array structure in order to store all of that information."

## Key metaphor(s)

- The `[]` in `name="fruits[]"` is the signal that turns a bunch of checkboxes into one array — "we always want to put these square brackets here" when getting input from multiple checkboxes.
- An array is "just a container that can hold multiple pieces of information" — exactly what a multi-select needs.

## Gotchas

- The `[]` at the end of the `name` is **essential** — without it, PHP keeps only the last value instead of collecting them all into an array.
- **Only checked** boxes get submitted — unchecked ones simply don't appear in `$_POST["fruits"]` at all.
- Values arrive in the order the user **checked** them, not the order they appear on the page.
- The superglobal must match the method: this form is `post`, so it's `$_POST["fruits"]`, not `$_GET`.

## Modern note

Loop over the array instead of hard-coding indexes (see chapter 25 for loops):

```php
<?php
foreach ($_POST["fruits"] ?? [] as $fruit) {
    echo $fruit . "<br>";
}
?>
```

- `?? []` avoids a warning when nothing was checked (there's no `fruits` key at all in `$_POST`).
- You can use the short array syntax for the same idea: the form side is unchanged; only the PHP collection logic grows.

## Checkpoint

1. Why do the checkbox inputs all share the name `fruits[]`?
2. What happens if you remove the `[]`? Predict, then test.
3. Build the fruit form, check two boxes, and echo them in the order you checked them.
4. If the user checks nothing, what is in `$_POST["fruits"]`?

[← Chapter 15 — Arrays](15-arrays.md) | [Course Map](../README.md) | [Chapter 17 — Associative Arrays →](17-associative-arrays.md)
