# Chapter 10 — Getting User Input

**Video section 10 (1:05:14)**

## What the video teaches

Up to now, every program has been built around data that *we* hard-coded into the file. But "a lot of times in our PHP programs, we're going to be dealing with all sorts of information and data" — and "a lot of times, we're going to want to be able to get that information and that data from a user." Any good website lets the user interact with it: filling out forms, doing all sorts of stuff on the website. So this section shows how PHP can get input from users who type information into things like **text boxes** or **buttons** — really anything like that.

### Step 1 — Set up a form

To get input in PHP you first need something called a **form**. If you know HTML you may already understand it; if not, here's the explanation: a form is "basically a special HTML element that's going to allow the user to input information, and then it will be able to pass the information that the user enters over to our PHP programs."

The form is "kind of like the **middleman between HTML and PHP**" — it's where HTML and PHP meet. It's essentially just a way that PHP can get information from a user.

You set up a form, then inside of it you can put text boxes, radio buttons, submit buttons — really anything you want. The user interacts with those, and PHP is able to get what the user enters.

Mike types out the form tag and gives it two HTML attributes:

```html
<form action="site.php" method="get">
    ...
</form>
```

- **`action`** — `action="site.php"`. This holds "the name of the php page that we want to be able to handle this form." `site.php` is the file the course has been using all along, and it's where Mike wants to handle what happens with the form. So you basically just put the name of the PHP page you want to work with.
- **`method`** — `method="get"`. This "essentially tells this form what we're trying to do with it." Here we're trying to *get* information from the user — the whole purpose of having this form is to get information from the user and use it in PHP. So `get` basically means "we're trying to get information."
- The form is closed off with a `</form>` closing tag.

### Step 2 — Add an input tag (a text box)

Now, inside the form, Mike can put HTML elements that let the user interact with the page — something to type into or a button to press.

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name">
    <input type="submit">
</form>
```

- `<input>` is "a special tag that can be used with these forms." The input tag allows the user to input information, and "since it's special, it'll work with the form in order to pass that information back to PHP."
- **`type="text"`** — gives us a basic text box.
- **`name="name"`** — "you want to make sure that this is a name that's going to describe what type of content you're getting. And also, this name needs to be unique." For this program we ask the user to enter their name, so the input is called `name`.
- **`Name:`** — the plain text typed before the box is a *prompt*: "this will kind of tell us what this textbox is for." Refreshing the page shows the text box asking for the name.

### Step 3 — Add a submit button

One more thing is needed: a submit button, so that once the user types in their information they can click a button and that information gets submitted.

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name">
    <input type="submit">
</form>
```

- `<input type="submit">` creates the Submit button. It is special: it "basically just submits all the information in the text boxes up here." When you click it, it submits the information to PHP, and "we'll be able to access all the information that got submitted in our PHP program."

### Step 4 — Read the input in PHP with `$_GET`

Now the HTML side is done; it's time to get access to all the information entered in the form. Mike takes his PHP tags and moves them *below* the form (putting a `<br>` tag above them):

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name">
    <input type="submit">
</form>
<br>
<?php
    echo $_GET["name"];
?>
```

What happens when you click Submit: the form gets submitted, and you can access the information that got submitted inside the PHP program.

- `echo` echoes something out into the HTML document (as we've used all along).
- `$_GET` — "this stands for get, so this is basically going to get the information that got submitted."
- `["name"]` — the square brackets hold, in quotation marks, "the name of the input that I want to grab" — the `name` attribute given to the input tag. Here the input for the name was given the name `name`, so you type that in.

So `$_GET["name"]` prints out the value that got submitted inside that text box. In the browser Mike types `Mike` into the box, clicks Submit, and the page echoes `Mike` — "it was actually pretty easy, right?"

### The name is arbitrary — but it must match

"All we had to do was set up this form, and then down here we said `get` and we passed in the name of the input tag." You can make that name whatever you want:

- Change the input to `name="username"`, and change the PHP to `$_GET["username"]`, and it does the same thing.
- "That name is pretty arbitrary, like it can be whatever you want it to be — it just has to match." The name in the PHP has to match the name in the HTML.

That's basically how you get input from a user: set up a form, let the user enter information in the text box, and when they click Submit the `$_GET` field gets populated with the user's name so you can print it out.

### Interweaving input into the page

Instead of a bare value, Mike makes it read like a real sentence by concatenating text before the value:

```php
<?php
echo "Your name is: " . $_GET["name"];
?>
```

- `.` is PHP's **concatenation** operator — it joins the string `"Your name is: "` and the submitted value together. Now the page reads "Your name is: Mike."
- "Essentially, we're taking the information that we got from the form, and we're sort of like interweaving it into our HTML document, just like that."

### Adding a second field: age

"You can really do this for as much information as you want." Mike adds another piece of information — the user's age:

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name"><br>
    Age: <input type="number" name="age"><br>
    <input type="submit">
</form>
<br>
<?php
    echo "Your name is: " . $_GET["name"] . "<br>";
    echo "Your age is: " . $_GET["age"];
?>
```

- A `<br>` break tag is added after the first prompt, then `Age:` followed by another input.
- `type="number"` is used this time, because it's an age (a number-only box).
- The input is named `age`, so down in PHP he uses `$_GET["age"]`.
- He copies the "Your name is:" line, adds a break tag, and changes the text to "Your age is:" with `$_GET["age"]`.

Now two pieces of information can be entered at once — a name and an age. Typing `john` and `30` and clicking Submit populates both fields: "Your name is: john / Your age is: 30."

### The complete picture

"That's sort of the basics of getting input from users. And you know, really, this is just scratching the surface — obviously you can get more complex with the types of information that you're getting and the amount of information that you're getting. But this basic concept is going to apply in every aspect of PHP."

The flow, in Mike's own summary:
1. Set up a form.
2. Use `action`, which "is just going to point to the current page" (`site.php`).
3. Use the `get` method.
4. Down below, when you want to access the submitted information: `$_GET` followed by the name of the input that was submitted.

## Key metaphor(s)

> The form is "kind of like the middleman between HTML and PHP." It's "where HTML and PHP meet" — a way PHP can get information from a user.

## Gotchas

- The input's `name` attribute is **arbitrary** — you can call it anything (`name`, `username`, ...) — but it **must match exactly** between the HTML `name="..."` and the PHP `$_GET["..."]`.
- Put the PHP that reads the form **after** the form tag in the file (Mike moves his PHP tags below the form with a `<br>`).
- The prompt text (`Name:`) is just plain HTML text — it has nothing to do with the data; the `name` attribute is what matters.
- Without a submit button, the user has no way to send the data.

## Modern note

- `$_GET` values arrive as **strings** — even numbers typed into a `type="number"` box come through as strings. Cast when you need math: `(int)$_GET["age"]`.
- If the user visits the page without submitting the form, `$_GET["name"]` doesn't exist, and echoing it causes a warning. Guard with the **null coalescing operator**:

```php
<?php
$name = $_GET["name"] ?? "Unknown";
?>
```

- Never echo raw user input into an HTML page — escape it with `htmlspecialchars()` (details in chapter 26's security note).
- Prefer typed inputs: `type="email"`, `type="date"`, etc. validate in the browser and give `$_GET`/`$_POST` cleaner values.

## Checkpoint

1. What are the three parts of a form that collect, name, and submit data?
2. How do you read the value of an input named `name`?
3. Build the form, submit it, and see your name and age echoed back.
4. What happens if you change the `name` attribute in the HTML but not in the PHP? Predict, then test.
5. Why does the form's `method` say `get`?

[← Chapter 09 — Working With Numbers](09-working-with-numbers.md) | [Course Map](../README.md) | [Chapter 11 — Building a Basic Calculator →](11-building-a-basic-calculator.md)
