# Chapter 11 — Building a Basic Calculator

**Video section 11 (1:15:37)**

## What the video teaches

Your first real program: a calculator. "We're basically going to design a little program where the user can enter in two numbers, and then our program will add those two numbers together and print out the results." It's a pretty simple calculator, but it shows "how we can get numbers from the user in PHP" — and it introduces a key PHP concept hiding in the URL.

### The starting point

Mike already has a basic form set up (from the last tutorial):

```html
<form action="site.php" method="get">
    ...
    <input type="submit">
</form>
```

- `action="site.php"` — the name of the file he's currently on, which handles the form.
- `method="get"` — explained in the previous tutorial.
- The Submit button, which submits the form so PHP can access whatever the user entered.

### Building the form — two number inputs

The first thing needed is to get two numbers from the user:

```html
<form action="site.php" method="get">
    <input type="number" name="num1"><br>
    <input type="number" name="num2">
    <input type="submit">
</form>
```

- There's a special HTML type for numbers: `type="number"`. "Type equal to number is basically going to make it so the user can only enter in a number, so they're not going to be able to enter in text."
- The first input is given `name="num1"` — "this is going to be the first number that they're going to enter."
- After a `<br>` break tag, the same exact input is repeated with `name="num2"`.
- When the Submit button is clicked, the information inside *both* of these boxes gets submitted, and PHP can access it.

### The PHP — adding the two numbers

In the PHP section, Mike accesses `num1` and `num2` and adds them together:

```php
<?php
echo "Answer: " . ($_GET["num1"] + $_GET["num2"]);
?>
```

What this does, step by step:
- `echo` prints a result out into the HTML.
- `$_GET["num1"]` "is going to get whatever the user typed into that first number box, and it's going to put it over here."
- The `+` plus sign adds `num2` onto it: `$_GET["num1"] + $_GET["num2"]`.
- "Essentially, what we're saying here is I want to echo out into the HTML num1 plus num2."
- **The key trick:** "because both of these were entered in as numbers — in other words, because I said the type of input was going to be a number — PHP will actually add these numbers together as if they were actual numbers." So entering `2` and `3` produces `5`, not the string "23".
- The `"Answer: " .` prefix makes the output readable — "we'll be printing out the answers."

### Running it

Refreshing the page shows the two text boxes and the Submit button. The page reads "Answer: 0" — "that's basically just because we haven't entered in any numbers yet."

Entering `10` in the first box and `21` in the second, then clicking Submit, prints **31**. "So that is essentially how we can go about adding two numbers together."

### The cool thing — the URL

Now the video's big reveal: "if I was to make this browser window a little bit bigger, you'll notice up here inside of the URL, we have this little line over here that says `num1=10` ampersand `num2=21`."

```
site.php?num1=10&num2=21
```

- "Essentially what this is doing is it's telling us what the values of those variables were."
- "So with PHP, this can actually get added on to the URL."
- If he changes `num2` to `50` up in the URL and presses Enter, "this is actually going to change the information that gets entered in" — the answer updates **without typing anything into the form**.
- "This is one of the key concepts in PHP." Anytime information is entered with a form, when the form gets submitted, that information appears up in the URL. When you load the page, you can give these different pieces of information: setting `num1=100` in the URL makes number one `100`, and it adds the two numbers together.
- If he clears the query string and hits Enter, "the whole form gets reset." Putting in `40` and `30` and clicking Submit "is essentially just adding these things on to the end of the URL. And this information is basically telling us what this answer's going to be."
- "That's not like, you know, too important later in the course — we can actually leverage those URLs to do different things. But like I said, I'm going to talk more about that later" (that's chapter 13, URL parameters). Mike just wanted to mention it "so you guys aren't confused if you see that stuff up there in the URL."

The program works well: different numbers can be added together. "And this kind of shows you — instead of getting text — how we can get numbers, and we can actually do math on those numbers in our PHP program."

## Key metaphor(s)

- The URL query string is the form's report card: `site.php?num1=10&num2=21` "is telling us what the values of those variables were."
- `type="number"` is what guarantees `+` means *addition* and not string-joining.

## Gotchas

- Before any input, the answer shows as `0` — no values have been submitted yet.
- The `?num1=...&num2=...` at the end of the URL is normal and expected — don't be confused when you see it.
- The `?` separates the page from the parameters; the `&` separates multiple parameters from each other.
- `+` only does math because both values came from `type="number"` inputs. If they came from text boxes, `+` would glue the strings together instead (the classic PHP "2+3=23" trap).
- The answer can be changed just by editing the URL — data sent with GET is not tamper-proof (built on fully in chapters 13 and 14).

## Modern note

`$_GET` values are *strings*, so make the math robust by casting to `int` and defaulting to `0` when the parameter is missing:

```php
<?php
$num1 = (int)($_GET["num1"] ?? 0);
$num2 = (int)($_GET["num2"] ?? 0);
echo "Answer: " . ($num1 + $num2);
?>
```

- `(int)` converts whatever came in to a whole number.
- `?? 0` avoids a warning on the very first load, when no values are in the URL.

## Checkpoint

1. Why does `+` add the two values as numbers instead of gluing them together?
2. Change the values in the URL by hand and watch the answer change — without touching the form.
3. Add a third number field (`name="num3"`) and update the PHP to add all three.
4. What do `?` and `&` do in `site.php?num1=10&num2=21`?

[← Chapter 10 — Getting User Input](10-getting-user-input.md) | [Course Map](../README.md) | [Chapter 12 — Building a Mad Libs Game →](12-building-a-mad-libs-game.md)
