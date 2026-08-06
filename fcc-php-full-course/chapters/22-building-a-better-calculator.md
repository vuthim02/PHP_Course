# Chapter 22 — Building a Better Calculator

**Video section 22 (2:47:13)**

## What the video teaches

Back in chapter 11 we built a very basic calculator: enter two numbers, it **adds** them and prints the answer. This tutorial upgrades it into a **fully functional four-function calculator** — addition, subtraction, multiplication, *and* division — where **the user decides which operation to perform**. And we'll get to use if statements to pull it off: the if statement will look at what the user typed for the operator and respond accordingly.

### The HTML form

The form's `action` is `site.php` (the current file), `method` is `post`, and it has a submit button. Then we add input boxes for three pieces of information:

```html
<form action="site.php" method="post">
    First Num: <input type="number" name="num1"><br>
    Second Num: <input type="number" name="num2"><br>
    Operator: <input type="text" name="op">
    <input type="submit">
</form>
```

- `<input type="number" name="num1">` — gets the **first number**.
- `<input type="number" name="num2">` — copied from the first, gets the **second number**.
- `<input type="text" name="op">` — `op` stands for **operator**. This one is text, so the user can type a `+`, `-`, `*`, or `/` sign. (Mike initially writes `type="textbox"` by mistake, then corrects it to `type="text"`.)
- The submit button triggers the POST.

"We're looking for three pieces of information: the first number, the second number, and the operation they want to perform."

### Grabbing the input

```php
<?php
$num1 = $_POST["num1"];
$num2 = $_POST["num2"];
$op   = $_POST["op"];
?>
```

- `$num1` and `$num2` hold numbers; `$op` holds a string of text (the operator symbol).
- "So our job now is to get that information, store it inside of different variables, and then we need to figure out what operation they wanted us to perform."

### Deciding the operation with an if statement

```php
<?php
if ($op == "+") {
    echo $num1 + $num2;
} elseif ($op == "-") {
    echo $num1 - $num2;
} elseif ($op == "/") {
    echo $num1 / $num2;
} elseif ($op == "*") {
    echo $num1 * $num2;
} else {
    echo "Invalid Operator";
}
?>
```

- `if ($op == "+")` — is the operator a plus sign? If yes, echo the **sum** `$num1 + $num2`.
- `elseif ($op == "-")` — is it a minus sign? If yes, echo `$num1 - $num2`.
- `elseif ($op == "/")` — forward slash = division → echo `$num1 / $num2`.
- `elseif ($op == "*")` — asterisk = multiplication → echo `$num1 * $num2`.
- `else` — "there's one more situation that could occur, and that's when the user entered an invalid operator" → echo `Invalid Operator`.

The if statement "allows me to figure out what's inside of the operator variable... depending on what they entered, I can perform that operation."

### Testing it

- Adding: enter two numbers and `+` → the program looks through the if statement, finds the matching operator, and computes the sum.
- **Multiplying**: `30` × `2` → prints `60`. "You can see down here that we do — so that's actually working pretty well."
- **Invalid operator**: enter something nonsensical like `draf` as the operator → the program falls through every `elseif`, hits the `else`, and prints `Invalid Operator`. "Our program will recognize that and it will show us [the error]."

"Down here we were able to use this if statement in order to figure out what the operator was that the user entered — and that is actually pretty awesome."

### The decimal gotcha — an HTML limitation, not PHP

If you type a decimal like `4.6` into a `type="number"` input, the browser rejects it: *"Please enter a valid value"* — and it helpfully points out "the nearest values are 4 and 5." This has nothing to do with PHP:

- By default, `<input type="number">` only accepts **whole numbers**.
- The fix is the `step` attribute:

```html
<input type="number" step="0.1" name="num1">
```

- `step="0.1"` says "we can take numbers to this decimal point" — numbers to the 10s place. Now `4.6 + 5.0` works.
- But `4.567` still errors, "because the step is not that significant."
- Set `step="0.001"` and even `4.567` is accepted, so `4.567 + 9` computes fine.

"That's not necessarily a PHP limitation, that's more of an HTML limitation. If you were a little bit confused about that, hopefully that clears it up."

### The runnable project file in this course

The project file `projects/07-better-calculator.php` uses a **dropdown** (values `add`/`sub`/`mul`/`div`) and the **GET** method so you can test it easily from the address bar, with the exact same if/elseif/else logic:

```php
<form action="07-better-calculator.php" method="get">
    First Num: <input type="number" step="0.1" name="num1"><br>
    Second Num: <input type="number" step="0.1" name="num2"><br>
    Operation:
    <select name="op">
        <option value="add">Add</option>
        <option value="sub">Subtract</option>
        <option value="mul">Multiply</option>
        <option value="div">Divide</option>
    </select>
    <input type="submit">
</form>
```

```php
<?php
$num1 = $_GET["num1"];
$num2 = $_GET["num2"];
$op   = $_GET["op"];

if ($op == "add") {
    echo $num1 + $num2;
} elseif ($op == "sub") {
    echo $num1 - $num2;
} elseif ($op == "mul") {
    echo $num1 * $num2;
} elseif ($op == "div") {
    echo $num1 / $num2;
} else {
    echo "Invalid Operator";
}
?>
```

The logic is identical to the video's — only the operator values (`"add"`, `"sub"`, `"mul"`, `"div"` instead of `"+"`, `"-"`, `"/"`, `"*"`) and the transport (GET instead of POST) differ.

## Key metaphor(s)

> The if statement acts as a **switchboard for the operator** — it looks at what the user typed and routes the program to the matching arithmetic operation.

## Gotchas

- The browser's "Please enter a valid value" rejection of decimals is **HTML behavior** of `type="number"`, not a PHP error — fix it with `step`, e.g. `step="0.1"`.
- The operator string must **match exactly** one of the branches; anything else falls through to `else` and prints `Invalid Operator`.
- The math only runs inside the branch whose operator matched — if nothing matches, no arithmetic happens at all.

## Modern note

This if/elseif/else chain is the textbook case for the **`match` expression** (PHP 8) — cleaner, strict, and usable as a value:

```php
<?php
$result = match ($op) {
    "+" => $num1 + $num2,
    "-" => $num1 - $num2,
    "/" => $num1 / $num2,
    "*" => $num1 * $num2,
    default => "Invalid Operator",
};
echo $result;
?>
```

`match` does **strict** comparison (`===`), needs no `break`, and throws on no-default/no-match. Know the if/else version from the video first, then adopt `match`.

## Checkpoint

1. Why does the page print `Invalid Operator` for anything that isn't one of the four symbols?
2. What does `step="0.1"` do, and why is it needed for a `type="number"` input?
3. Build the calculator and test all four operators plus an invalid one — what do you see each time?
4. If the user enters `10`, `5`, and `*`, walk through the if statement and say exactly which branch runs and what it echoes.
5. Rewrite the PHP using `match` and confirm the same output.

[← Chapter 21 — If Statements (continued) — Comparisons](21-if-statements-cont.md) | [Course Map](../README.md) | [Chapter 23 — Switch Statements →](23-switch-statements.md)
