# Deep Study Notes — "PHP Programming Language Tutorial - Full Course"

**Source:** https://www.youtube.com/watch?v=OK_JCtrrv-c (video ID `OK_JCtrrv-c`)
**Instructor:** Mike Dane (Giraffe Academy) on freeCodeCamp.org
**Length:** 4h 37m (276 min 39 s) | **Published:** June 20, 2018
**Version used in video:** PHP 7.1 | **Our environment:** PHP 8.5.9 (Linux, OPcache)

These notes were produced by studying the full transcript section by section. Each section lists the concepts, the code taught, the instructor's key metaphors, and the gotchas. Every section ends with a **"Modern PHP (8.5)"** note reconciling the 2018 lesson with current PHP — the code in those boxes is tested against PHP 8.5 syntax.

---

## Course Map

The video is a beginner's tour of PHP: setup → syntax → web forms → data structures → control flow → OOP. In our course it maps almost entirely onto **Level 1** and the first three chapters of **Level 2**.

| Video section | Course chapter |
|---|---|
| 1–5 Introduction, Install, Editor, Hello World, HTML | L1 `01-php-installation`, `02-syntax-and-structure` |
| 6–7 Variables, Data Types | L1 `03-variables-and-data-types`, `04-constants` |
| 8–9 Strings, Numbers | L1 `11-strings`, `12-numbers-and-math`, `09-built-in-functions` |
| 10–14 Forms, GET/POST, URL params | L1 `14-forms-and-user-input`, `18-superglobals` |
| 15–17 Arrays, Checkboxes, Assoc. arrays | L1 `10-arrays` |
| 18–19 Functions, Return | L1 `08-functions` |
| 20–23 If/else, Comparisons, Calculator, Switch | L1 `05-operators`, `06-control-flow` |
| 24–25 While, For loops | L1 `07-loops` |
| 26 Comments | L1 `02-syntax-and-structure` |
| 27–28 Include/require | L1 `17-includes-and-requires` |
| 29–33 OOP | L2 `01-oop-fundamentals`, `02-constructors`, `03-inheritance` |

---

## 1. (0:00) Introduction

- PHP is a **server-side language**: it sits on the web server and interacts with the client to make websites more powerful.
- Integrates tightly with **HTML** — you can write PHP right alongside HTML.
- Used by millions of websites/web apps on the back end.
- Course roadmap: install → first file → PHP+HTML → form input → general programming concepts (if/loops/arrays) → OOP (classes/objects).

**Key metaphor:** PHP is a language that "sits on the web server" and makes websites more powerful.

## 2. (1:56) Windows Installation

- Download from **php.net → Downloads → Windows Downloads**; pick the version matching your OS (in the video: 64-bit **thread-safe** PHP 7.1 zip).
- Extract to `C:\PHP` (or anywhere).
- Configure the Windows **PATH variable**: Start → "edit the system environment variables" → Environment Variables → Edit `Path` → New → add `C:\PHP`.
- Verify: `echo %PATH%` then `php -v` (should print a version number).

**Key metaphor:** The PATH variable "tells Windows where a bunch of executable files are."

**Gotcha:** Don't modify any files inside the PHP folder.

> **Modern PHP (8.5):** On Linux you don't manually manage PATH — use your package manager (Fedora: `dnf install php`). Our setup already runs `php -v` cleanly. `php -S` still ships the same built-in dev server used in section 4.

## 3. (7:32) Choosing a Text Editor

- Any text editor that can save a `.php` file works; no special configuration needed.
- Editors with **syntax highlighting** and error display help a lot.
- Instructor uses **Atom** (GitHub). Notepad/TextEdit are the minimum.
- Advice: pick the editor you're comfortable with (VS Code is the modern default).

## 4. (11:06) Hello World & Setup

- Start the built-in dev server: `php -S localhost:4000` (don't close this terminal).
- The server reports `document root` — the folder PHP serves files from.
- Create `site.php` in the doc root.
- **PHP tags:** `<?php ... ?>` — everything inside is executed as PHP.
- `echo` prints text to the page; every statement ends with a **semicolon**.
- Browser URL: `localhost:4000/site.php`.

```php
<?php
echo "Hello World";
?>
```

**Key metaphors:** localhost = "the web address of your local computer." A PHP file is just an HTML file that can also contain PHP blocks.

**Gotchas:** Semicolons are mandatory after every statement. Keep the server running or the page dies. PHP executes top to bottom on every request/refresh.

> **Modern PHP (8.5):** Still exactly the same. For CLI-only quick checks you can also use `php -r 'echo "Hello";'`. The short echo tag `<?= "Hello" ?>` is enabled by default in modern PHP (the video never mentions it — worth knowing).

## 5. (20:29) Writing HTML

- `echo` can print **valid HTML**; it renders in the browser.
- Instructions execute **in order** — reordering the code changes the page output.
- `echo` works with or without parentheses: `echo("...")` ≡ `echo "..."`.

```php
<?php
echo "<h1>Mike's Site</h1>";
echo "<hr>";
echo "<p>This is my site</p>";
?>
```

**How it works:** when the browser requests the page, the web server executes all PHP blocks, then serves the resulting HTML. Refresh = re-execute.

**Gotcha:** The semicolon after every instruction "tells PHP we're done with this line."

> **Modern PHP (8.5):** Prefer `<?= ?>` for short HTML injections, and in real projects let a template engine (Blade, Twig) handle markup. The execution-order concept is unchanged.

## 6. (27:30) Variables

- **Variable = a container** that stores a piece of information.
- Syntax: `$name = value;` — the **dollar sign is required**.
- Text needs quotes; numbers don't.
- **Interpolation:** inside double-quoted strings, `$var` is replaced by its value.
- Variables can be reassigned mid-program; one change updates every usage.

```php
<?php
$characterName = "John";
$characterAge = 35;

echo "There once was a man named $characterName<br>";
echo "He was $characterAge years old<br>";
echo "He really liked the name $characterName<br>";
echo "But didn't like being $characterAge.<br>";

$characterName = "Mike";
echo "But then his name changed to $characterName<br>";
?>
```

**Key metaphor:** Variable = "a container where we can store pieces of information." Use descriptive names so you know what's inside.

**Gotcha:** Without variables, changing a value repeated 100× in a long story means editing 100 places (error-prone). With a variable, change it in one place.

> **Modern PHP (8.5):** Prefer **single quotes** when you don't need interpolation (slightly faster, avoids surprises). Double quotes still interpolate `$var`. For robustness use `{$characterName}` or concatenation in complex cases. Naming convention today: `$characterName` (camelCase) — the video's style is fine.

## 7. (38:09) Data Types

Basic types:

| Type | What it holds | Example |
|---|---|---|
| `string` | plain text, in quotes | `"To be or not to be"` |
| `int` | whole number (no decimal) | `30`, `-5` |
| `float` | decimal number | `3.7` |
| `bool` | `true` or `false` | `true` |
| `null` | no value | `null` |

```php
<?php
$phrase  = "To be or not to be";   // string
$age     = 30;                     // integer
$gpa     = 3.7;                    // float
$isMale  = true;                   // boolean
$nothing = null;                   // null
?>
```

**Key metaphor:** Boolean is "a binary data type" — only ever true or false.

**Gotcha:** "There is a big difference in PHP between `30` and `30.0`." PHP distinguishes ints from floats. 99% of the time you only need text, numbers, booleans.

> **Modern PHP (8.5):** Real types in modern PHP also include arrays, objects, `iterable`, `mixed`, and `enum`. With **typed properties** you declare the type once and PHP enforces it (see section 29). There is also `get_debug_type()` / `var_dump()` to inspect a value's type.

## 8. (44:27) Working With Strings

Functions and techniques taught:

- `strtolower($s)` / `strtoupper($s)` — lowercase / uppercase
- `strlen($s)` — number of characters
- Indexing with square brackets: `$s[0]` — **0-indexed**
- Modify a character: `$s[0] = "B";`
- `str_replace("old", "new", $s)` — replace substring
- `substr($s, $start)` / `substr($s, $start, $length)` — substring

```php
<?php
$phrase = "Draft Academy";

echo strtolower($phrase);          // draft academy
echo strtoupper($phrase);          // DRAFT ACADEMY
echo strlen($phrase);              // 15
echo $phrase[0];                   // D (index 0)
echo $phrase[1];                   // r

$phrase[0] = "B";                  // "Braft Academy"
echo $phrase;

echo str_replace("Draft", "Panda", $phrase);  // "Panda Academy"

echo substr($phrase, 8);           // "Academy"
echo substr($phrase, 8, 3);        // "Aca"
?>
```

**Key metaphor:** Functions are "little snippets of code we can call" to get information about or modify a string.

**Gotchas:** Indexing starts at **zero**. Argument order for `str_replace`: (find, replace, subject). There are many more string functions — search "PHP string functions."

> **Modern PHP (8.5):** Same functions. Additions: `str_contains()`, `str_starts_with()`, `str_ends_with()` (PHP 8+), `str_contains` is the modern "does string contain substring." Multi-byte safety: use the `mb_*` family (`mb_strlen`, `mb_substr`) for non-ASCII text.

## 9. (54:50) Working With Numbers

- Arithmetic: `+ - / *` and **modulus** `%` (remainder).
- Order of operations: parentheses override precedence (`(4 + 5) * 10`).
- Increment/decrement: `++`, `--`.
- Shorthand assignment: `+=`, `-=`, `/=`, `*=` (`$num += 25` ≡ `$num = $num + 25`).
- Math functions: `abs()`, `pow()`, `sqrt()`, `max()`, `min()`, `round()`, `ceil()`, `floor()`.

```php
<?php
echo 10 % 3;             // 1  (10 mod 3 = remainder)
echo 4 + 5 * 10;         // 54 (multiplication first)
echo (4 + 5) * 10;       // 90 (parentheses override)

$num = 10;
$num++;                  // 11
$num += 25;              // = 35

echo abs(-100);          // 100
echo pow(2, 4);          // 16
echo sqrt(144);          // 12
echo max(2, 10);         // 10
echo round(3.7);         // 4
echo ceil(3.3);          // 4
echo floor(3.9);         // 3
?>
```

**Gotchas:** `%` gives the **remainder**, not the quotient. `ceil` always rounds up, `floor` always down, `round` to nearest.

> **Modern PHP (8.5):** For money/precision math use `int` cents or BCMath (`bcadd`, `bcmul`) — covered in our L1 `12-numbers-and-math` and L1 ch12 (BCMath). `%` with negatives can be surprising; `fmod()` for floats.

## 10. (1:05:14) Getting User Input

- An HTML **form** is "the middleman between HTML and PHP."
- Form attributes: `action` (which page handles it), `method="get"`.
- Input types: `text`, `number`, `submit`.
- The **`$_GET` superglobal**: `$_GET["name"]` reads the submitted value by the input's `name`.

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name"><br>
    Age: <input type="number" name="age"><br>
    <input type="submit">
</form>
```

```php
<?php
echo "Your name is: " . $_GET["name"] . "<br>";
echo "Your age is: " . $_GET["age"];
?>
```

**Key metaphor:** The form is "the middleman between HTML and PHP."

**Gotchas:** The input's `name` attribute **must match** the key in `$_GET`. Put the PHP that reads the form *after* the form tag.

> **Modern PHP (8.5):** Same mechanics. Additions: `$_GET` keys may not exist → use the **null coalescing operator** `$_GET["name"] ?? "Unknown"` (PHP 7+) instead of error-prone direct access. Never echo raw input without escaping (`htmlspecialchars()` or `htmlspecialchars($_POST["x"], ENT_QUOTES)`) — more in section 26 and our L2 `26-web-security`.

## 11. (1:15:37) Building a Basic Calculator

- Two `<input type="number">` fields + `$_GET` retrieval, add with `+`.
- With `type="number"`, PHP adds them as actual numbers.
- Submitting puts a **query string** in the URL: `site.php?num1=10&num2=21` — editing it changes the result.

```html
<form action="site.php" method="get">
    <input type="number" name="num1"><br>
    <input type="number" name="num2">
    <input type="submit">
</form>
```

```php
<?php
echo "Answer: " . ($_GET["num1"] + $_GET["num2"]);
?>
```

**Gotchas:** Before any input, the answer shows as 0. The `?num1=...&num2=...` query string is a key concept — don't be confused when you see it in the URL.

> **Modern PHP (8.5):** Cast inputs explicitly: `(int)$_GET["num1"]` (GET always arrives as strings). The `??` operator: `($_GET["num1"] ?? 0) + ($_GET["num2"] ?? 0)`.

## 12. (1:22:13) Building a Mad Libs Game

- Capture three free-text inputs into variables via `$_GET`, then interpolate into a story.

```html
<form action="site.php" method="get">
    Color: <input type="text" name="color"><br>
    Plural Noun: <input type="text" name="pluralNoun"><br>
    Celebrity: <input type="text" name="celebrity"><br>
    <input type="submit">
</form>
```

```php
<?php
$color       = $_GET["color"];
$pluralNoun  = $_GET["pluralNoun"];
$celebrity   = $_GET["celebrity"];

echo "Roses are $color<br>";
echo "$pluralNoun are blue<br>";
echo "I love $celebrity<br>";
?>
```

**Gotchas:** Before submission the story prints blanks. Fixing that requires checking whether the form was submitted — covered later (section 20+ and `isset()`).

> **Modern PHP (8.5):** Guard with `isset($_GET["color"])` or `?? ""` to avoid notices on first load.

## 13. (1:28:59) URL Parameters

- A **URL parameter** is a value on the URL: `site.php?name=Mike`. `?` separates page from params, `&` separates multiple.
- Values are readable/editable in the URL; accessed via `$_GET`.
- Useful for bookmarking state (e.g., Google search URLs `?q=dogs`).
- **Not secure** — motivates POST.

```php
<?php
echo $_GET["name"];
echo $_GET["age"];
?>
```
```
URL: site.php?name=Mike&age=70
```

**Key metaphor:** URL parameters let you "give information to your PHP page without having to make the user do it."

**Gotcha:** GET data can be tampered with directly in the address bar.

> **Modern PHP (8.5):** `http_build_query(["name" => "Mike", "age" => 70])` to build such URLs safely. URL-encode with `urlencode()` / `rawurlencode()`.

## 14. (1:35:52) POST vs GET

- `<input type="password">` masks typed characters.
- **GET** puts data in the URL (visible, editable, insecure).
- **POST** sends data between client and server "in a more secure fashion" — nothing in the URL; accessed via **`$_POST`**.
- Convention: most developers prefer **POST for forms**; GET for URL parameters.

```html
<form action="site.php" method="post">
    <input type="password" name="password">
    <input type="submit">
</form>
```

```php
<?php
echo $_POST["password"];
?>
```

**Key metaphor:** "GET is just kind of like anything goes — anyone can see the information, it's up there in the URL."

**Gotchas:** The form `method` must match the superglobal used (`$_GET` vs `$_POST`). POST can carry more data than GET.

> **Modern PHP (8.5):** Note POST is "more secure" only about *visibility*, not *encryption* — HTTPS is what encrypts. Real apps never echo a password. `$_REQUEST` (the video's later "better calculator" implicitly relies on it) combines GET+POST+COOKIE — use the explicit superglobal instead. Modern forms should include `required`, `minlength`, and a CSRF token (L2 `22-authentication`).

## 15. (1:41:44) Arrays

- An **array** stores *multiple* values (a variable stores one).
- Created with `array(...)`, comma-separated; can mix types.
- Access by **index starting at 0**: `$friends[0]`.
- Modify: `$friends[1] = "Dwight";` Add at any index: `$friends[4] = "Angela";`
- `count($array)` returns the number of elements.

```php
<?php
$friends = array("Kevin", "Karen", "Oscar", "Jim");

echo $friends[0];            // Kevin
echo $friends[1];            // Karen

$friends[1] = "Dwight";      // modify
echo $friends[1];            // Dwight

$friends[4] = "Angela";      // add at index 4
echo count($friends);        // 5
?>
```

**Key metaphor:** "Very similar to a variable but an array can store more than one piece of information" — up to thousands/millions of values.

**Gotchas:** Indexing starts at **zero**. Echoing an entire array just prints "Array."

> **Modern PHP (8.5):** Prefer the shorthand `$friends = ["Kevin", "Karen"];`. Use `var_dump()` / `print_r()` to inspect arrays. Push with `[] =` or `array_push()`. New-ish: `array_is_list()` (PHP 8.1) to check a numeric array.

## 16. (1:50:26) Using Checkboxes

- Checkbox inputs: `type="checkbox"`, a `name` ending in **`[]`** so all checked values collect into one array, plus a `value` attribute per box.
- Retrieve via `$_POST["fruits"]` → an array; access with indexes.

```html
<form action="site.php" method="post">
    <input type="checkbox" name="fruits[]" value="apples">Apples<br>
    <input type="checkbox" name="fruits[]" value="oranges">Oranges<br>
    <input type="checkbox" name="fruits[]" value="pears">Pears<br>
    <input type="submit">
</form>
```

```php
<?php
$fruits = $_POST["fruits"];
echo $fruits[0];   // first checked fruit
echo $fruits[1];   // second checked fruit
?>
```

**Gotchas:** The `[]` in the name is essential — it collects checked values into one array. Values arrive in the order checked. Only **checked** boxes are submitted.

> **Modern PHP (8.5):** Loop instead of hard-coding indexes: `foreach ($_POST["fruits"] ?? [] as $fruit) { echo $fruit . "<br>"; }` — see `foreach` in section 25.

## 17. (1:57:22) Associative Arrays

- An **associative array** stores **key → value** pairs using `=>`.
- Access by **key**: `$grades["Jim"]`.
- Keys must be **unique**; values may repeat.
- Modify by key; `count()` works.
- Practical app: form posts a student name → look up their grade.

```php
<?php
$grades = array("Jim" => "A+", "Pam" => "B-", "Oscar" => "C+");

echo $grades["Jim"];     // A+
echo $grades["Pam"];     // B-

$grades["Jim"] = "F";    // modify by key
echo count($grades);     // 3
?>
```
```html
<form action="site.php" method="post">
    <input type="text" name="student">
    <input type="submit">
</form>
```
```php
<?php
$grade = $_POST["student"];
echo $grades[$grade];
?>
```

**Gotchas:** "You always want to make sure these keys are unique" — duplicate keys are ambiguous. Values can repeat.

> **Modern PHP (8.5):** Shorthand `["Jim" => "A+", ...]`. Guard lookups: `$grades[$grade] ?? "Not found"`. `foreach ($grades as $key => $value)` is the idiomatic way to iterate. `array_key_exists()` vs `isset()` distinction matters when a value is `null`.

## 18. (2:04:55) Functions

- A **function** is a reusable container for code that performs a specific task.
- Syntax: `function name($param1, $param2) { ... }`.
- Code runs only when you **call** it: `name(args);`.
- **Parameters** let you pass information in; as many as you want.

```php
<?php
function sayHi($name, $age) {
    echo "Hello $name, you are $age<br>";
}

sayHi("Tom", 40);
sayHi("Dave", 13);
sayHi("Oscar", 80);
?>
```

**Key metaphor:** A function is "a special container where we can put a bunch of code designed to perform a specific task"; write it once, use it many times.

**Gotchas:** Defining a function produces no output — you must call it. Call arguments must match the declared parameter order.

> **Modern PHP (8.5):** Add **parameter + return type declarations**: `function sayHi(string $name, int $age): void { ... }`. Declare `declare(strict_types=1);` at the top of files to enforce them. **Named arguments** (PHP 8): `sayHi(age: 40, name: "Tom")`.

## 19. (2:12:10) Return Statements

- `return` sends a value back to the caller.
- A function can be used in an expression/assignment: `$cubeResult = cube(4);`
- `return` **breaks out of the function** — code after it never executes.
- You can return any type, or `return;` to just exit.

```php
<?php
function cube($num) {
    return $num * $num * $num;
}

$cubeResult = cube(4);
echo $cubeResult;        // 64
echo cube(4);            // 64
?>
```

**Gotchas:** "Whenever we put this return keyword in there, it's always going to be the last line of the function" — code after return is dead code. Without `return`, the function returns nothing.

> **Modern PHP (8.5):** Declare the return type: `function cube(int $num): int { return $num ** 3; }` (`**` is the modern power operator). **Arrow functions** `fn($x) => $x * 2` for one-liners (PHP 7.4+).

## 20. (2:19:10) If Statements

- `if (condition) { ... }`, `else { ... }`, `elseif` (or `else if`).
- Conditions resolve to true/false (booleans work directly).
- Logical operators: **`&&` (AND)**, **`||` (OR)**, **`!` (negation)**.
- Real-life analogy: "If I'm hungry, I eat breakfast"; "If it's cloudy, umbrella, otherwise sunglasses."

```php
<?php
$isMale = true;
$isTall = true;

if ($isMale && $isTall) {
    echo "You are a tall male";
} elseif ($isMale && !$isTall) {
    echo "You are a short male";
} elseif (!$isMale && $isTall) {
    echo "You are not male but are tall";
} else {
    echo "You are not male and not tall";
}
?>
```

**Key metaphor:** If statements make "our programs a lot smarter" by letting them respond to data.

**Gotchas:** With `&&`, both sides must be true. `else` runs only when no earlier condition matched.

> **Modern PHP (8.5):** Use `elseif` (one word). Logical **`and`/`or`** keywords have lower precedence than `&&`/`||` — prefer `&&`/`||` for predictability.

## 21. (2:37:16) If Statements (cont.) — Comparisons

- Comparison operators: `>`, `<`, `>=`, `<=`, `==`, `!=`.
- Comparisons resolve to true/false, so they work directly as conditions.
- Build your own `getMax()` (instead of built-in `max()`), then a 3-arg version with elseif chains.

```php
<?php
function getMax($num1, $num2) {
    if ($num1 > $num2) {
        return $num1;
    } else {
        return $num2;
    }
}
echo getMax(3, 90);          // 90

function getMax3($num1, $num2, $num3) {
    if ($num1 >= $num2 && $num1 >= $num3) {
        return $num1;
    } elseif ($num2 >= $num1 && $num2 >= $num3) {
        return $num2;
    } else {
        return $num3;
    }
}
echo getMax3(300, 900, 400); // 900
?>
```

**Gotchas:** A comparison "gets resolved down to a true or false value" — that's why it can be a condition. Be careful: `==` (equality) vs `=` (assignment).

> **Modern PHP (8.5):** Prefer **strict comparison** `===` / `!==` (type + value) to avoid `"0" == 0` type-juggling surprises. The whole if/else chain can become `max($num1, $num2, $num3)`, or better, the **spaceship operator** `<=>` and `match` expression (PHP 8) — see section 23's modern note.

## 22. (2:47:13) Building a Better Calculator

- Four-function calculator: two number inputs + an operator string.
- `if / elseif / else` chain comparing the operator to `"+"`, `"-"`, `"/"`, `"*"`; `else` → "Invalid Operator".
- HTML `type="number"` by default only accepts whole numbers; add `step="0.1"` to allow decimals — an **HTML** limitation, not PHP.

```html
<form action="site.php" method="post">
    <input type="number" step="0.1" name="num1"><br>
    <input type="number" step="0.1" name="num2"><br>
    <input type="text" name="op">
    <input type="submit">
</form>
```

```php
<?php
$num1 = $_POST["num1"];
$num2 = $_POST["num2"];
$op   = $_POST["op"];

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

**Gotchas:** The "Please enter a valid value" rejection is an **HTML** behavior of `type="number"`, fixed with `step`, not a PHP issue.

> **Modern PHP (8.5):** This whole chain is the textbook case for the **`match` expression** (PHP 8):
> ```php
> $result = match ($op) {
>     "+" => $num1 + $num2,
>     "-" => $num1 - $num2,
>     "/" => $num1 / $num2,
>     "*" => $num1 * $num2,
>     default => "Invalid Operator",
> };
> echo $result;
> ```

## 23. (2:56:53) Switch Statements

- `switch ($value) { case "X": ... break; ... default: ... }`
- For comparing **one value against many cases**.
- `break` exits the switch (prevents falling through).
- `default` handles unmatched values.
- Everything a switch does can be done with if/else — switch is just cleaner.

```php
<?php
$grade = $_POST["grade"];

switch ($grade) {
    case "A":
        echo "You did amazing";
        break;
    case "B":
        echo "You did pretty good";
        break;
    case "C":
        echo "You did poorly";
        break;
    case "D":
        echo "You did very bad";
        break;
    case "F":
        echo "You fail";
        break;
    default:
        echo "Invalid Grade";
}
?>
```

**Gotchas:** Without `break`, execution continues through later cases after a match. Without `default`, unmatched values print nothing.

> **Modern PHP (8.5):** **`match`** (PHP 8) is the modern replacement — it's an expression (can be assigned), doesn't require `break`, and does strict comparison:
> ```php
> $msg = match ($grade) {
>     "A" => "You did amazing",
>     "B" => "You did pretty good",
>     "C" => "You did poorly",
>     "D" => "You did very bad",
>     "F" => "You fail",
>     default => "Invalid Grade",
> };
> ```

## 24. (3:05:09) While Loops

- `while (condition) { body }` — loops while condition is true.
- Two parts: **condition** (checked before each iteration) and **body**.
- Counter pattern: `$index` + `$index++`.
- **Infinite loop** warning if nothing makes the condition false.
- **do-while:** `do { body } while (condition);` — body runs first, condition checked after (at least one run guaranteed).

```php
<?php
$index = 1;
while ($index <= 5) {
    echo $index;        // 12345
    $index++;
}

// do-while: executes at least once
$index = 6;
do {
    echo $index;        // 6
    $index++;
} while ($index <= 5);
?>
```

**Gotchas:** Infinite loops ("just a bunch of ones... you could scroll down infinitely") can slow your computer — close the browser tab. While checks the condition **first**; do-while checks it **last**.

> **Modern PHP (8.5):** Same semantics. Prefer `for` or `foreach` when the iteration count is known. Always make sure the increment/termination line is reachable.

## 25. (3:15:18) For Loops

- `for (initialization; condition; increment) { body }` — three parts in the parens.
- More compact than the equivalent while loop.
- Classic use: **loop through an array** with `count()` as the boundary.

```php
<?php
for ($i = 1; $i <= 5; $i++) {
    echo $i;                 // 12345
}

$luckyNumbers = array(4, 8, 15, 16, 23, 42);
for ($i = 0; $i < count($luckyNumbers); $i++) {
    echo $luckyNumbers[$i];
}
?>
```

**Gotchas:** Array indexes start at 0 → initialize `$i = 0` and use `< count(...)` (strict less-than); the last valid index is `count-1`.

> **Modern PHP (8.5):** For iterating arrays, **`foreach`** is the idiomatic tool (the video never taught it — important gap):
> ```php
> foreach ($luckyNumbers as $num) {
>     echo $num;
> }
> foreach ($grades as $student => $grade) {
>     echo "$student got $grade<br>";
> }
> ```
> Other loop aids: `break`, `continue`, and `range(0, 5)`.

## 26. (3:26:24) Comments

- `//` single-line comment (rest of line ignored).
- `/* ... */` block comment (multi-line).
- Comments are for humans, not the computer — notes, to-dos, describing code.
- **Commenting out** code (prefix with `//`) disables it without deleting — great for debugging.

```php
<?php
// This is a single-line comment
/* This is a
   multi-line comment block */
echo "Hello";          // inline comment after code
// echo "This line is commented out and won't run";
?>
```

**Gotchas:** `//` comments out only the rest of *that line*; use `/* */` for multi-line.

> **Modern PHP (8.5):** Same. Docblocks `/** @param string $name */` are used for documentation/IDE tooling.

## 27. (3:31:08) Including HTML

- `include "header.html";` pulls another file's contents into the current page at that spot.
- DRY pattern: put shared **header**/**footer** in their own files, include on every page.
- Change once, updates everywhere — makes a site **modular**.

```html
<!-- header.html -->
<h1>Mike's Website</h1>
<hr>
```
```html
<!-- footer.html -->
<hr>
Thanks for visiting!
```
```php
<?php
include "header.html";
echo "<p>Some article content...</p>";
include "footer.html";
?>
```

**Key metaphor:** Breaking a website into "little reusable components" (header, footer, nav) that you "insert into your different pages using include statements."

**Gotchas:** Updating one header file updates 100+ pages that include it.

> **Modern PHP (8.5):** Also learn the distinction: `include` (warn, continue) vs `require` (fatal on missing) vs `include_once`/`require_once` (avoid double-inclusion). Covered fully in L1 `17-includes-and-requires`.

## 28. (3:36:51) Include: PHP

- Include **PHP files** — their variables and functions become usable in the including file.
- **Template pattern:** an `articleHeader.php` echoes variables (`$title`, `$author`, `$wordCount`) without defining them; the page assigns those variables *before* including.
- Utility pattern: a file defining a function + variable, both usable after include.

```php
<!-- articleHeader.php -->
<h2><?php echo $title; ?></h2>
<h4><?php echo $author; ?></h4>
Word count: <?php echo $wordCount; ?>
```
```php
<?php
// site.php — assign values BEFORE the include
$title = "My First Post";
$author = "Mike";
$wordCount = 400;
include "articleHeader.php";
?>
```
```php
<?php
// useful-tools.php
$feetInMile = 5280;
function sayHi($name) {
    echo "Hello $name";
}
?>
```
```php
<?php
// site.php
include "useful-tools.php";
sayHi("Mike");          // Hello Mike
echo $feetInMile;       // 5280
?>
```

**Gotchas:** Variables used inside an included file must be assigned in the *including* file **before** the `include` runs (order matters).

> **Modern PHP (8.5):** This is the seed of template engines and **autoloading**. Today you'd use Composer's PSR-4 autoloader + namespaces instead of manual includes (L2 `14-composer-autoloading`, `16-namespaces`). But the mental model — reusable partials populated with data — is exactly how Blade/Twig components work.

## 29. (3:45:57) Classes & Objects

- **Class** = a specification/blueprint for a **custom data type** (strings/numbers/booleans can't model real-world entities like books).
- **Attributes** declared with `var $name;` inside the class.
- **Object** = an instance of a class, created with `new Book`.
- Access/set properties with the **arrow operator** `->`: `$book1->title = "Harry Potter";`
- Class names conventionally start with a capital letter.

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;
}

$book1 = new Book;
$book1->title  = "Harry Potter";
$book1->author = "JK Rowling";
$book1->pages  = 400;

$book2 = new Book;
$book2->title  = "Lord of the Rings";
$book2->author = "Tolkien";
$book2->pages  = 700;

echo $book1->title;    // Harry Potter
echo $book2->author;   // Tolkien
?>
```

**Key metaphor:** A class is "a blueprint, a template for what a book is"; an object is "an instance of a class" — the actual book.

**Gotchas:** Without a class you have *no way* to represent a book in the program — that's the whole motivation for custom data types.

> **Modern PHP (8.5):** Replace `var` with **typed properties**:
> ```php
> class Book {
>     public string $title;
>     public string $author;
>     public int $pages;
> }
> ```
> `var` still works (alias of `public`) but is legacy. Full OOP treatment: L2 `01-oop-fundamentals`.

## 30. (3:56:23) Constructors

- **`__construct()`** (two underscores + `construct`, exact name required) is called automatically whenever an object is created with `new`.
- Takes parameters; commonly initializes attributes up front, saving lines of manual assignment.
- **`$this`** refers to the current object being created.

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;

    function __construct($aTitle, $aAuthor, $aPages) {
        $this->title  = $aTitle;
        $this->author = $aAuthor;
        $this->pages  = $aPages;
    }
}

$book1 = new Book("Harry Potter", "JK Rowling", 400);
$book2 = new Book("Lord of the Rings", "Tolkien", 700);

echo $book1->title;    // Harry Potter
echo $book2->author;   // Tolkien
?>
```

**Gotchas:** The method "needs to be named `__construct`... if you don't name it exactly like that, this isn't going to work." Creation drops from ~8 lines per book to 2. You can still modify after construction (`$book1->title = "Hunger Games";`).

> **Modern PHP (8.5):** **Constructor property promotion** (PHP 8) collapses this even further:
> ```php
> class Book {
>     public function __construct(
>         public string $title,
>         public string $author,
>         public int $pages,
>     ) {}
> }
> ```

## 31. (4:06:18) Object Functions

- An **object function (method)** is a function defined *inside* a class; every instance can call it via `->`.
- Inside the method, **`$this->`** accesses the *calling object's* attributes — same method behaves differently per object.
- Methods can return values (booleans here).

```php
<?php
class Student {
    var $name;
    var $major;
    var $gpa;

    function __construct($name, $major, $gpa) {
        $this->name  = $name;
        $this->major = $major;
        $this->gpa   = $gpa;
    }

    function hasHonors() {
        if ($this->gpa >= 3.5) {
            return "true";
        }
        return "false";
    }
}

$student1 = new Student("Jim", "Business", 2.8);
$student2 = new Student("Pam", "Art", 3.6);

echo $student1->hasHonors();   // false (2.8 < 3.5)
echo $student2->hasHonors();   // true  (3.6 >= 3.5)
?>
```

**Gotchas:** `$this->gpa` "always refers to the GPA of the object that's calling the function." Note the instructor returns strings `"true"`/`"false"` because `echo` can't display a raw boolean — you'd normally return a real boolean.

> **Modern PHP (8.5):** Return a real `bool` and echo with a ternary, or use **return type declarations**:
> ```php
> function hasHonors(): bool {
>     return $this->gpa >= 3.5;
> }
> echo $student2->hasHonors() ? "true" : "false";
> ```

## 32. (4:13:52) Getters & Setters

- **Visibility:** `public` (accessible to any code) vs `private` (only inside the declaring class). Accessing a private property from outside → fatal error.
- **Getter** — a function that returns a private attribute (`getRating()`).
- **Setter** — a function that sets a private attribute, often with **validation** (`setRating()` filters invalid values to a default).
- Best practice: have the **constructor call the setter** so validation applies everywhere.
- Note: the `var` keyword used earlier is effectively `public`.

```php
<?php
class Movie {
    public $title;
    private $rating;

    function __construct($title, $rating) {
        $this->title = $title;
        $this->setRating($rating);   // route through setter for validation
    }

    function getRating() {
        return $this->rating;
    }

    function setRating($rating) {
        if ($rating == "G" || $rating == "PG" || $rating == "PG-13" ||
            $rating == "R" || $rating == "NR") {
            $this->rating = $rating;
        } else {
            $this->rating = "NR";    // invalid ratings default to Not Rated
        }
    }
}

$avengers = new Movie("Avengers", "PG-13");
echo $avengers->getRating();         // PG-13
$avengers->setRating("dog");         // invalid -> filtered
echo $avengers->getRating();         // NR
?>
```

**Gotchas:** `public` = "open to everybody"; `private` = "only code inside the class it's declared in can use it." Making `rating` private breaks direct access — getters/setters restore it *with* control. A movie can never hold an invalid rating once the constructor uses the setter.

> **Modern PHP (8.5):** Modern PHP favors simple **typed public properties** over getter/setter boilerplate (esp. with `readonly` for immutable data). Getters/setters are still valuable for validation. Options:
> ```php
> class Movie {
>     public function __construct(
>         public string $title,
>         private string $rating = "NR",
>     ) {
>         $this->rating = $this->sanitizeRating($rating);
>     }
>     // ... or use a readonly property + enum
> }
> ```
> **Enums** (PHP 8.1) are the idiomatic way to model a fixed set like G/PG/PG-13/R/NR — covered in L2 and our reference.

## 33. (4:29:17) Inheritance

- **`extends`** lets a class inherit all attributes and methods of another: `class ItalianChef extends Chef { }`.
- The child can use inherited methods without redefining them.
- The child can **add new methods** the parent lacks.
- **Overriding:** redefine a method with the same name in the child to replace inherited behavior.

```php
<?php
class Chef {
    function makeChicken() {
        echo "The chef makes chicken<br>";
    }
    function makeSalad() {
        echo "The chef makes salad<br>";
    }
    function makeSpecialDish() {
        echo "The chef makes bbq ribs<br>";
    }
}

class ItalianChef extends Chef {
    function makePasta() {
        echo "The chef makes pasta<br>";
    }
    function makeSpecialDish() {          // overriding inherited method
        echo "The chef makes chicken parm<br>";
    }
}

$chef = new Chef();
$chef->makeChicken();       // The chef makes chicken

$italianChef = new ItalianChef();
$italianChef->makeChicken();          // inherited from Chef
$italianChef->makePasta();            // only ItalianChef has this
$italianChef->makeSpecialDish();      // overridden: chicken parm
// $chef->makePasta();                // ERROR: Chef has no makePasta
?>
```

**Key metaphor:** Inheritance = "where a class can inherit all the functionality, all the attributes from another class"; used when the child can do everything the parent can "plus a bunch of other stuff."

**Gotchas:** A child calling a method it didn't define works *because* of inheritance. The parent cannot use child-only methods. Overriding = redefining a same-named method in the child.

> **Modern PHP (8.5):** Prefer **composition + interfaces/traits** over deep inheritance hierarchies (L2 `03-inheritance`, `04-traits-and-interfaces`). `parent::` calls parent methods; `final` prevents further inheritance; methods/properties have `protected` as the third visibility level.

---

## What the Video Teaches vs. What It Omits

The video is an excellent, heavily hands-on beginner tour, but it was built on PHP 7.1 in 2018. Compared to our course and the `PHP-Complete-Reference.md`, it omits:

| Missing | Where it's covered in our course |
|---|---|
| `foreach`, `match`, `??`, arrow functions | L1 `07-loops`, `05-operators`, L1 ch08 |
| Typed properties, property promotion, readonly | L2 `01-oop-fundamentals` |
| Strict typing (`declare(strict_types=1)`) | L1 `03-variables-and-data-types` |
| `include` vs `require` vs `*_once` | L1 `17-includes-and-requires` |
| Escape output (`htmlspecialchars`), XSS, SQL injection | L2 `13-sql-injection-prevention`, `26-web-security` |
| `isset()` / `empty()` for form guards | L1 `18-superglobals` |
| Sessions, cookies, file uploads | L2 `19-file-uploads`, `22-authentication` |
| PDO / MySQL | L2 `11-pdo-database`, `12-mysql-mariadb` |
| Composer, autoloading, namespaces | L2 `14-composer-autoloading`, `16-namespaces` |
| Enums, match, named args, JIT | L3+ and `PHP-Complete-Reference.md` |

**Bottom line:** Master the 33 sections here as your mental foundation, then let Level 1–2 of the course convert the 2018 syntax into modern PHP 8.5 idioms.

---

## Verification

All code blocks in the **"Modern PHP (8.5)"** notes use current syntax. To run any section's example on this machine:

```bash
php -r 'echo strtoupper("dog");'          # DOG
php -r '$a=[1,2,3]; foreach($a as $n) echo $n;'   # 123
```

Run the classic video examples through `test.php` or `php -S localhost:4000` to see them in a browser.
