# Chapter 13 — URL Parameters

**Video section 13 (1:28:59)**

## What the video teaches

A **URL parameter** is "basically just a value that we can tack on to the end of one of our URLs, which will essentially pass a value into our PHP program, and then we can access it." This section shows how that works and what it's doing.

### The starting program

Mike has a very simple program set up — the same kind of form from chapter 10:

```html
<form action="site.php" method="get">
    Name: <input type="text" name="name">
    <input type="submit">
</form>
```

```php
<?php
echo $_GET["name"];
?>
```

- `action="site.php"` — the page he's currently working on.
- `method="get"` — "whenever we're using these URL parameters, you always want to make sure that this says get right there." (In the next video he talks more about what `get` is actually doing, and there's another method called `post`.)
- The form just asks the user to enter their name in a text box, with a submit button, and the PHP below prints the name onto the browser.

Typing `Mike` and clicking Submit prints `Mike` on the page — very simple.

### What's in the URL

Now the reveal: expanding the browser window, the address bar reads:

```
site.php?name=Mike
```

"Essentially, what happened was, when I submitted that form, the value for name actually got placed inside of our URL." This is a **URL parameter** — also called a **URL variable** or a **URL value**. "Basically what this means is, this is just the piece of information that we're giving to PHP."

### You can bypass the text box entirely

Since the value lives in the URL, you can change it directly there:

- Change `name=Mike` to `name=Dave` and hit Enter — "the value updates down here" on the page, without touching the form.
- Even better, you can "bypass this textbox altogether" and pass a value straight into the URL — type `name=john` in the address bar and that becomes the value the page receives.

Mike's point: "up until this point in the course, we've always been getting our information through these text boxes, and that's a very common way to do it. A lot of times you're going to want your user to interact with the website using things like text boxes or buttons. But other times, in our PHP programs, you might want to give information to your php page without having to make the user do it." In certain circumstances "there might just be certain values that I want to give in a specific URL, and I don't necessarily want the user to have to enter them."

### Anatomy of the URL

To add another URL parameter:

```
site.php?name=Mike&age=70
```

- The **`?`** (question mark) "sort of delineates these two things" — it separates the page name from the parameters.
- Then you write the name of the parameter/variable, an `=`, and the value being given to it: `name=Mike`.
- To add another one, use the **`&`** (ampersand) and repeat the pattern: `&age=70`.
- Now, in addition to giving the page the `name` value, we're also giving it an `age` value of `70`.

At first, adding the new parameter doesn't change anything visible on the page — "even though I added that new parameter up here in the URL, it doesn't really change anything on the page. But inside of our PHP, it's going to change a lot," because that value can now be accessed.

### Reading the second parameter

Mike changes the PHP to read the age instead of the name:

```php
<?php
echo $_GET["age"];
?>
```

Because the variable `age` was passed in the URL, PHP can print it — the page now prints `70`. And if the parameter is removed from the URL, "it's just not going to print out anything because it didn't receive that value."

### Why this is so useful

"This is a really awesome way for us to build these URLs." One of the reasons it's so useful: "you could have a webpage that has a bunch of values associated with it, and then you could store all of those values in the URL. So a user could actually bookmark that page, and they could go back to that page with all of that same information set for the page." It doesn't have to be someone's name or age — "this could be any information that you want to store on a particular web page."

Real websites do this all the time. Mike demonstrates a Google search for "dogs": on Enter, the Google URL has something similar to what he did — an ampersand and little values like `q=dogs`, `aqs=chrome...` etc. "Google is doing similar things inside of the Google URL... the concept is the same, we can store information inside of these URLs."

### The catch — it's not secure

"Now, here's one of the problems with something like this: it's not very secure. All of the information that we pass into this website is basically visible." If you type your name and click Submit, the name is "basically visible and available up here in the URL." In many circumstances you *want* it visible (e.g., for bookmarking). But in other circumstances you don't want the user to be able to see the information — "or even be able to modify it, like I can just modify this piece of information and it's going to change what happens on my website."

"For a situation like that, we can actually use another form method. So you'll notice up here inside of my form, I have this little method attribute that says `get`. There's another method called `post`, which we can use, which will basically do the same thing but in a more secure fashion. And in the next tutorial, I'm going to talk to you guys about what that POST method can do, and we'll talk about the differences between GET and POST."

## Key metaphor(s)

> URL parameters let you "give information to your PHP page without having to make the user do it."

The `?` is the door between the page and its data; `&` is the connector that stacks up more data.

## Gotchas

- The form's method **must** say `get` for URL parameters to work this way.
- GET data can be tampered with directly in the address bar — anyone can edit it, and the page responds to the change.
- If a parameter isn't present in the URL, echoing it prints nothing (and on modern PHP, raises a warning).
- Values arrive as **strings** even if they look like numbers (`age=70` is the string `"70"`).
- The parameter list grows without limit — each `key=value` joined by `&`.

## Modern note

- Build URLs safely with `http_build_query()` — it handles encoding of spaces and special characters for you:

```php
<?php
$url = "site.php?" . http_build_query(["name" => "Mike", "age" => 70]);
echo $url;   // site.php?name=Mike&age=70
?>
```

- URL-encode individual values that contain spaces/special chars with `urlencode()` / `rawurlencode()`.
- Don't put sensitive data in URL parameters at all — they end up in browser history, server logs, and can be shared/linked accidentally.
- If you need *some* state in a URL but want it hard to read/guess, sign it or use opaque identifiers rather than raw values.

## Checkpoint

1. What do `?` and `&` mean in a URL?
2. What superglobal reads URL parameters?
3. Open `site.php?name=Mike&age=70`, then edit the URL by hand and reload.
4. Why would you *not* put a password in the URL?
5. What real website can you think of that stores state in its URL? Check a search engine's address bar.

[← Chapter 12 — Building a Mad Libs Game](12-building-a-mad-libs-game.md) | [Course Map](../README.md) | [Chapter 14 — POST vs GET →](14-post-vs-get.md)
