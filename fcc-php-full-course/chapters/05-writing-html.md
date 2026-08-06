# Chapter 05 — Writing HTML

**Video section 5 (20:29)**

## What the video teaches

Mike breaks down the `echo` command from the previous chapter and shows off what it can really do — including writing full HTML from inside your PHP code. This chapter is where the PHP-and-HTML relationship from chapter 4 starts paying off.

### Recap: what `echo` actually does

- Last chapter you typed `echo("Hello World");` and refreshed the browser to see "Hello World".
- If you **view the page source**, you'll see that "Hello World" is literally **printed inside the actual HTML document**. The echoed text got placed into the HTML file that the server sent to the browser.
- So `echo` is "basically a command in PHP which allows us to **write information out onto the HTML document**" — it lets you write HTML out to your HTML files.

### The syntax details

- The basic shape is `echo` followed by parentheses, then the content in quotes, then a semicolon: `echo("...");`
- The **semicolon is really important**: "Anytime we write a line of code in PHP, we want to make sure that we include that semicolon." The semicolon tells PHP "we're done writing this line of code, and now we're going to write another line of code."
- **Parentheses are optional** with `echo`. `echo("...")` and `echo "..."` both work — "I could get rid of these parentheses, and you could just leave it like this." So either style is valid.

### You can echo HTML itself

In addition to regular text, you can type **HTML code inside the quotation marks**, and the browser will actually render it. This is the cool part:

```php
<?php
echo "<h1>Mike's Site</h1>";
echo "<hr>";
echo "<p>This is my site</p>";
?>
```

Line by line:

1. `echo "<h1>Mike's Site</h1>";` — echoes an `h1` heading. When you refresh the page, "Mike's Site" gets rendered as an `h1`. Viewing the page source confirms the `h1` is actually placed into the page.
2. `echo "<hr>";` — echoes a horizontal rule (the thin line across the page).
3. `echo "<p>This is my site</p>";` — echoes a paragraph.

So "inside of these quotation marks, I can put any valid HTML that I want, and it'll actually get rendered over there on the browser." From inside your PHP code, you can write out an entire website, and you can include as much text or as much HTML as you want.

Mike notes this will come in handy throughout the course: any time he wants to print something out or show something, he uses this `echo` command.

### How the code gets executed (the mental model)

When you request (or refresh) the page, here's what happens:

1. The little web server you set up **serves the page** — you can see the URL `www/site.php` in the address bar.
2. The web server goes into the **PHP blocks** in the file and **executes all the PHP code** inside them.
3. The output of that code is **placed into the file**.
4. The result is a **finished HTML file**, which is what your browser renders.

So the PHP code runs server-side on every request, and the browser only ever sees the finished HTML output.

### Instructions execute in order

PHP executes your instructions **top to bottom, in the order you wrote them**:

- The page shows "Mike's Site", then the horizontal rule, then the paragraph — exactly the order the `echo` statements appear in the file.
- If you move the paragraph line up above the `h1`, the paragraph shows up first. Reorder the code → reorder the output. "PHP is going to execute these instructions in order: this instruction, then this instruction, and this instruction."

### The takeaway

This is the bare basics of PHP: when a user requests `site.php`, the PHP code gets executed, the file gets put together with all the HTML tags, and the browser sees the finished product. Right now you're using the simple `echo` instruction — but "as we go through this course, we're going to learn more and more complex instructions, which are going to allow us to do more and more complex things."

## Key metaphor(s)

> PHP code is executed by the server on each request, then the output is "placed into the file" — producing the finished HTML that the client sees.

`echo` is a channel that lets PHP inject content straight into the HTML document the browser receives.

## Gotchas

- **Semicolon after every instruction.** Without it, PHP doesn't know one line of code has ended.
- **Every page refresh re-executes all the PHP.** The output you see is generated fresh on each request.
- **Order matters.** The browser renders your echoes in the order the statements appear in the file.
- HTML tags inside the quotes get *rendered* by the browser — but if you echo text like `<h1>` into a context where you wanted the literal characters, you'd need to escape them. (Not an issue in the video's usage.)

## Modern note

For short HTML injections, modern PHP prefers the **short echo tag**, which is exactly `echo` in disguise:

```php
<h1><?= "Mike's Site" ?></h1>
```

This is identical to writing `<?php echo "Mike's Site"; ?>`. In real-world projects you'll usually keep the markup in the template and use a template engine (Blade/Twig) or just the short tag for small dynamic bits — but the underlying concept — echo writes into the HTML output — is unchanged in PHP 8.5.

## Checkpoint

1. Can `echo` print raw HTML? Show an example with three different tags.
2. What is the difference between `echo "..."` and `echo("...")`?
3. Explain step by step what happens on the server when you refresh `site.php`.
4. Retype the example above, add a third paragraph, then move one line to the top and predict — before running — how the order of output changes.

[← Chapter 04 — Hello World & Setup](04-hello-world-and-setup.md) | [Course Map](../README.md) | [Chapter 06 — Variables →](06-variables.md)
