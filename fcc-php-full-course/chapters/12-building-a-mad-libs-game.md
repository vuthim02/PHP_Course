# Chapter 12 — Building a Mad Libs Game

**Video section 12 (1:22:13)**

## What the video teaches

A **Mad Libs** game: "it's basically a game where you can enter in a bunch of random words, and then you'll take all those random words and kind of like sprinkle them in through a story. And usually, because you entered in a bunch of random stuff, the story ends up being like pretty funny." In a typical Mad Libs you enter different parts of speech — nouns, a person, a place — and all those entries get sprinkled throughout a story.

Mike is going to build one: let the user enter a bunch of different words, then put those words into a story.

### The base story

Down in his program Mike has a little story set up:

> Roses are red, violets are blue, I love you.

"It's kind of like a classic poem. But I think it would be a lot better if we Mad-Libbed it up and we allowed the user to enter in some random stuff." So instead:

- Instead of *red* → let the user enter a **custom color**.
- Instead of *violets are blue* → let the user enter their own **plural noun** ("... are blue").
- Instead of *I love you* → let the user enter a **celebrity** ("I love [celebrity]").

The story becomes: "Roses are [color], [plural noun] are blue, I love [celebrity]."

### The form — three text boxes

The form is already set up — `action="site.php"` (the name of the PHP file he's currently on) and `method="get"` — with a submit button, "a very basic form outline." Inside it, he prompts the user for a color, a plural noun, and a celebrity:

```html
<form action="site.php" method="get">
    Color: <input type="text" name="color"><br>
    Plural Noun: <input type="text" name="pluralNoun"><br>
    Celebrity: <input type="text" name="celebrity"><br>
    <input type="submit">
</form>
```

- Each is `type="text"` (a plain text box) with a prompt (`Color:`, `Plural Noun:`, `Celebrity:`) in front.
- The names match the prompts: `color`, `pluralNoun`, `celebrity`. "You can see I gave them all names to match. So this one's name is color, plural noun, and celebrity."
- So there are three text boxes, one asking for a color, one for a plural noun, and one for a celebrity.

### The PHP — store the input in variables

The last thing to do is grab that information when the form is submitted and put it into the story. Mike creates three variables, each storing the result of getting that input from the user:

```php
<?php
$color      = $_GET["color"];
$pluralNoun = $_GET["pluralNoun"];
$celebrity  = $_GET["celebrity"];
?>
```

- `$color = $_GET["color"]` — "when the user submits the form, this variable is going to get populated with whatever they entered in for the color."
- The same is done for the plural noun and the celebrity.
- Note that in PHP, `$_GET["pluralNoun"]` must spell the name exactly as it appears in the HTML's `name="pluralNoun"` — they have to match.

### The PHP — print them inside the story

Now that the variables exist, they're printed out inside the story:

```php
<?php
$color      = $_GET["color"];
$pluralNoun = $_GET["pluralNoun"];
$celebrity  = $_GET["celebrity"];

echo "Roses are $color<br>";
echo "$pluralNoun are blue<br>";
echo "I love $celebrity<br>";
?>
```

- `echo "Roses are $color"` — the variable `$color` is **interpolated** (expanded) inside the double-quoted string, so it prints the value stored in the variable.
- Same for the plural noun and the celebrity.
- "Essentially, I stored all of the things that the user input inside of these variables, and then down here I'm actually going to print them out inside of the story."

### Testing it

Before submitting, the story prints blanks: "Roses are [blank], [blank] are blue, I love [blank]." Mike enters `magenta` for the color, `microwaves` for the plural noun, and `Tom Hanks` for the celebrity, then clicks Submit.

All of that information gets submitted, stored inside each variable, and the variables get printed in the story:

```
Roses are magenta
microwaves are blue
I love Tom Hanks
```

"So we were actually able to make this Mad Libs now."

### The one problem (a preview of a later technique)

Mike points out the obvious flaw: "if I was to reset this form, this is showing up here before we actually submitted the form. So ideally, we would want this text to show up after we submit the form — after the user has entered in all the stuff and not before." He explains that later in the course they'll learn a technique to do something like that (that's the `isset()` / `$_SERVER` request-method check in the if-statements chapters). "But for now, this kind of works."

"You can see how we could essentially enter in whatever color, whatever plural noun, whatever celebrity we wanted, and it would show up inside of our story, just like that. So hopefully that makes sense. And what you should do is just build your own Mad Libs game — and you can model your own little Mad Libs story and sort of play around with it."

This chapter reuses everything from chapters 6 and 10: variables, string interpolation, forms, and `$_GET` — but now the values come from the user.

## Key metaphor(s)

- Mad Libs is like "sprinkling" random words into a story — the story is a template with slots the user fills in.
- The three variables are the middlemen: they catch the submitted words from `$_GET` and hand them to the story.

## Gotchas

- Before the form is submitted, the story prints **blank spaces** — no values have arrived yet. (The `isset()` fix appears later in the course.)
- The names must match exactly between the HTML inputs and the `$_GET` keys — `pluralNoun` is camelCase; misspelling it anywhere breaks the game silently.
- Inputs are `type="text"` here, so anything can be entered — that's the point of Mad Libs.

## Modern note

Guard against the "blanks on first load" problem with the null coalescing operator, so the story isn't built until values actually exist:

```php
<?php
$color      = $_GET["color"] ?? "";
$pluralNoun = $_GET["pluralNoun"] ?? "";
$celebrity  = $_GET["celebrity"] ?? "";

if ($color !== "" && $pluralNoun !== "" && $celebrity !== "") {
    echo "Roses are $color<br>";
    echo "$pluralNoun are blue<br>";
    echo "I love $celebrity<br>";
}
?>
```

A cleaner modern alternative is an `isset($_GET["color"])` / `if ($_SERVER["REQUEST_METHOD"] === "GET")` guard — the technique the video teases for later chapters.

## Checkpoint

1. Why does `echo "Roses are $color"` print the submitted color?
2. Build the Mad Libs game yourself and play it twice with different words.
3. Modify the poem — add a fourth word (e.g., a verb) to the form *and* to the story, and check that the names match.
4. What does the page show before you submit the form, and why?

[← Chapter 11 — Building a Basic Calculator](11-building-a-basic-calculator.md) | [Course Map](../README.md) | [Chapter 13 — URL Parameters →](13-url-parameters.md)
