# Chapter 27 — Including HTML

**Video section 27 (3:31:08)**

## What the video teaches

The **include statement** "basically allows us to include another file inside of our PHP file." You set up another PHP or HTML file, then use the `include` keyword to grab *all the code* from that other file and use it in your current file. This tutorial shows the basic use case: **define one header and one footer for your website, then include them on every page.**

### The problem: repeating a header and footer on 100 pages

"Let's say that when I'm creating my website, I want all the pages on my website to have the same header and the same footer." Imagine 100 pages — and you want to change the header and footer. "I wouldn't want to have to go and change it on 100 different places."

The PHP solution: write one **HTML file for the header** and one **HTML file for the footer**, then use PHP to **include the contents of those files into each one of your web pages.**

### Creating header.html and footer.html

Mike creates two files — "the world's simplest HTML files":

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

- The header is "basically just a header one, and then we have a horizontal rule" — an `<h1>` with "Mike's Website", then an `<hr>`.
- The footer is "again, just a horizontal rule, and then it says 'Thanks for visiting.'"
- "Obviously, in your own website, you can make the header and the footer as complex as your heart desires. For the purposes of this tutorial, I just created some simple headers and footers."

### Including them in the page

```php
<?php
include "header.html";
?>
<p>Some article content goes here.</p>
<?php
include "footer.html";
?>
```

- `include "header.html";` — "It'll go out, grab all the code from that file, and place it here into this PHP file." Just this one line and "all of the code for the header of my website is right here."
- Refresh the browser → the header shows up without you typing out any of its HTML.
- `include "footer.html";` — same idea for the footer; copy the line and change the filename.
- Content written in the page (the article text) shows up **in between** the header and the footer.

So the page renders: header (`<h1>` + `<hr>`) → your content → footer (`<hr>` + "Thanks for visiting!").

### Change it once, it updates everywhere

Now the payoff. Change the header file only — "instead of saying Mike's Website, we could say like **Mike's Cool Website**":

```html
<!-- header.html -->
<h1>Mike's Cool Website</h1>
<hr>
```

Refresh the page and the header has updated automatically — without touching any of the code inside the PHP page.

"The point is that if you are including the header on like 100 or 200 pages in your website, and you wanted to change it, you **only have to change it in one spot**, and it will automatically update on all of those other pages in your website. And that's why this is so powerful."

### Reusable components

"What a lot of people will do is they'll **break up their website into little reusable components**":

- the header of the website in its own file,
- the footer of the website in its own file,
- maybe a navigation list, or breadcrumbs, or whatever.

"You can place any of those things inside of their own files, and then you'll be able to use all of those different HTML components inside of your websites. So these includes are extremely useful." In short: "really what this does is it makes your website **more modular** — you can break your website up into these little components, and then you can just insert them into your different pages using those include statements."

And this is "just scratching the surface" — the next tutorial covers including **PHP** files too.

### The runnable demo in this course

The demo files in `demos/` use a slightly fuller header and footer, so the included page is a complete, valid HTML document. `header.html` holds the document setup (doctype, head, title, opening body) plus an `<h1>` with the brand, and `footer.html` closes the page out:

```html
<!-- demos/header.html -->
<!DOCTYPE html>
<html>
<head>
    <title>Giraffe Academy</title>
</head>
<body>
<h1>Giraffe Academy</h1>
<hr>
```

```html
<!-- demos/footer.html -->
<hr>
<p>Thanks for visiting!</p>
</body>
</html>
```

```php
<?php
// demos/including-html.php
include "header.html";
?>
<h1>Welcome</h1>
<?php
include "footer.html";
?>
```

The mechanics are exactly what the video teaches — `include "header.html";` at the top, your own content in the middle, `include "footer.html";` at the bottom.

## Key metaphor(s)

> `include` is like **copy-pasting by reference**: the included file's code is dropped into your page at that exact spot, and if the source file changes, every page that includes it changes too.

> Breaking a website into "**little reusable components**" that you "insert into your different pages using include statements" — that's what makes the site modular.

## Gotchas

- **Order matters**: the header include must come *before* the page content it opens, and the footer include goes at the very bottom.
- Updating one header file updates **every** page that includes it — that's the point, but it also means a broken header file breaks all of those pages at once.
- `include` points at a filename; if the file is in another folder, you must give the path (e.g. `include "partials/header.html";`).

## Modern note

- Learn the full `include` family:
  - `include` — warns but continues if the file is missing.
  - `require` — **fatal error** and stops the script if missing (use for essential files).
  - `include_once` / `require_once` — prevent the same file from being included twice.
- Modern template engines (Blade, Twig, and PHP's own view components) do the same job with layouts and components — the header/footer idea here is the seed of every layout system.

## Checkpoint

1. What does `include "header.html";` do when the page loads?
2. Why is it better than copying the header HTML into every page by hand?
3. If you rename the site in `header.html`, what happens to every page that includes it — and what do you *not* have to do?
4. Build a two-page site sharing one header and one footer via `include`.
5. What would happen if you used `require` instead of `include` and the header file was missing?

[← Chapter 26 — Comments](26-comments.md) | [Course Map](../README.md) | [Chapter 28 — Include: PHP →](28-include-php.md)
