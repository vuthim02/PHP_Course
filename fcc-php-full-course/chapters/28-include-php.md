# Chapter 28 — Include: PHP

**Video section 28 (3:36:51)**

## What the video teaches

The `include` statement "basically allows us to go out to another file and grab all the information in that file, and include it in our own file." The previous tutorial used `include` to pull in **HTML** from separate files to scaffold out a website. This tutorial takes that a step further: you can `include` **PHP files** inside your PHP files — and when you do, "things start to get really awesome."

### Pattern 1 — Template files populated with data

A blog has many posts, and you want every post's header to look the same. You build that shared header once in a file called `article-header.php`, and every blog post "includes" it.

The template file echoes variables it never defines:

```php
<h2><?php echo $title; ?></h2>
<h4><?php echo $author; ?></h4>
Word count: <?php echo $wordCount; ?>
```

Key idea: "I'm not actually putting any information in here. I'm just printing out the values of variables, but I didn't give any of these variables values yet."

Then in `site.php`, you `include` the template and — before the include runs — assign the variables their values:

```php
<?php
$title = "My First Post";
$author = "Mike";
$wordCount = 400;
include "article-header.php";
?>
```

Step by step, exactly as shown in the video:

1. Create a new file called `article-header.php`. It will act as the header for every article on the blog.
2. Add an `<h2>` and drop in PHP tags (`<?php ?>`) that `echo $title`. Add an `<h4>` that echoes `$author`. Add `Word count: ` followed by PHP tags that echo `$wordCount`.
3. In `site.php`, inside the PHP tags, write `include "article-header.php";`. Refresh the page: you get a "skeleton" — the header tags render but they're empty. Viewing the page source shows the `<h2>`, `<h4>`, and "Word count" label all present, just with no values. "We actually got all of that information from that other file."
4. Go back to `site.php` and, *above* the include, give the three variables values: `$title = "My First Post";` (your blog post title), `$author = "Mike";`, `$wordCount = 400;`.
5. Refresh again — the header is now fully populated. "I basically created a little template over here in this article-header.php file: we're gonna put the title in here, we're gonna put the author in here and the word count in here. But I didn't give those values — I'm actually letting the pages that include the article header assign those values."

Why it's powerful:

- The template decides **where** the title, author, and word count appear; the including file decides **what** they are.
- Create two, three, or four more blog-post files and each can include the exact same `article-header.php` with different `$title`, `$author`, and `$wordCount` values. "Even though I'm including the same file, depending on the file that I'm including it from, I can give it different information."
- To restyle every article header at once, edit only `article-header.php` — e.g. change the `<h2>` to an `<h1>`. No changes needed in `site.php`; "everything is still going to work correctly, but the styling will be updated."

### Pattern 2 — Utility files with functions and variables

Create `useful-tools.php` — a file of shared PHP code:

```php
<?php
$feetInMile = 5280;
function sayHi($name) {
    echo "Hello $name";
}
?>
```

This is a very simple file. `$feetInMile` is "basically how many feet there are in a mile" — a value you want to keep track of. `sayHi($name)` takes a name parameter and prints out "Hello" plus the name.

To use all of that functionality in `site.php`, just include the file:

```php
<?php
include "useful-tools.php";

sayHi("Mike");      // Hello Mike
echo $feetInMile;   // 5280
?>
```

- `sayHi("Mike")` prints **Hello Mike**. We were able to use the function even though we never wrote it in `site.php`.
- `echo $feetInMile;` prints **5280** — the variable came along with the include.
- "Even though I didn't write this function, and I didn't create this variable inside of my site.php file, I was still able to use that function and that variable, because I included this php file."

Common real-world practice: "a lot of times, what people will do is they'll create a file just like this useful-tools file, and they'll have a bunch of functions in it, or they'll have variables in there — a bunch of PHP code in there — and this is sort of like its own PHP file. And then when they want to use all that functionality, they'll just include the file inside of their PHP file, and then they can use those functions or those variables to do whatever they want."

### Summary — Mike's exact recap

"Those are two really powerful ways that you can use this include command. Over here with the article header, we basically created this little template, and then we let whoever was including it decide what the title, author, and word count would be. And then over here, we defined a function and a variable, and we were able to use that function and that variable just by including this file."

## Key metaphor(s)

- **Scaffolding**: `include` lets you "go out to another file, grab all the information in that file, and include it in our own file" — pulling separate pieces together to scaffold out a whole website.
- **Fill-in-the-blank template**: `article-header.php` is a reusable form that says "title goes here, author goes here, word count goes here." The page that includes it fills in the blanks. Same form, different data per page.
- **Utility drawer / toolbox**: `useful-tools.php` is a drawer of functions and variables; any file that includes it can reach in and use the tools.

## Gotchas

- **Order matters — big time.** The variables (`$title`, `$author`, `$wordCount`) are echoed *inside* the included template file, but they must be *assigned in the including file* **before** the `include` statement runs. Assign them after the include and the header renders empty.
- The template file alone produces nothing useful — viewing `article-header.php` by itself shows empty headers, because it uses variables it never defines.
- `include` works with both static HTML (chapter 27) and dynamic PHP (this chapter) — whatever is in the file gets included.

## Modern note

This is the seed of PHP's **autoloading**. In modern PHP you'd rarely hand-write `include` calls for classes — instead Composer's PSR-4 autoloader plus namespaces load classes for you (covered in Level 2 of `php-mastery-course`). But the mental model of *reusable templates populated with data* is exactly how template engines like Blade (Laravel) and Twig (Symfony) work with layouts, partials, and components.

Related include-family statements worth knowing (not covered in this video): `include_once` includes a file only once (avoids double-definition errors), `require` fails fatally if the file is missing (instead of emitting a warning and continuing), and `require_once` combines both behaviors.

## Checkpoint

1. In pattern 1, where do `$title`, `$author`, and `$wordCount` actually get their values?
2. What happens if you `include` the article header *before* assigning the variables? Why?
3. Why can a second blog-post file include the same `article-header.php` and still show different content?
4. Create your own `useful-tools.php` (e.g. a `$taxRate` variable and a `formatPrice()` function) and use both from a second page.
5. Run `php demos/include-php.php` and confirm you see the populated article header, "Hello Mike", and 5280.

[← Chapter 27 — Including HTML](27-including-html.md) | [Course Map](../README.md) | [Chapter 29 — Classes & Objects →](29-classes-and-objects.md)
