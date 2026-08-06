# Chapter 04 — Hello World & Setup

**Video section 4 (11:06)**

## What the video teaches

Mike gets your first PHP file up and running: he sets up a PHP server, creates a PHP file, and prints something onto the screen so you can confirm everything works.

### Why PHP runs on a web server

- "When we want to use PHP, we have to run PHP on a **web server**."
- PHP is a **server-side language** — a programming language that runs on a web server, and you can use it there to interact with your websites.
- Your website is basically **a collection of files that gets given to the user**. PHP on the web server handles giving out files to users, getting input from users, and doing stuff like that.
- "Basically, all you need to know is that PHP is going to run on a web server" — as a beginner, you don't need to understand more than that.
- The good news: **a web server comes with PHP**, so it's really easy to set up your own.

### Step 1 — Start the built-in PHP server

1. Open your terminal or command prompt. On **Mac** it's called the terminal; on **Windows** it's the command prompt (search "CMD"). Both are "programs which will allow us to interact with the computer using text commands."
2. Type:

```bash
php -S localhost:4000
```

and press Enter. Here's what that does:

- The `-S` flag tells PHP to start its built-in **development web server** ("we're using PHP, and PHP is going to create like a little web server for us").
- `localhost` is "essentially just like the web address of your local computer."
- `4000` is the **port** the server listens on.
- The server prints something like:
  - `PHP 7.1.1 Development Server started`
  - `Listening on http://localhost:4000` — a web server running on your local machine on port 4000.
  - `Document root is C:\Users\Mike D` — "that's basically where PHP is going to start looking for files that we're going to run." On a Mac it will probably be `Users/<your-username>`.
- **As a beginner, this built-in server is all you need.** Setting up another web server (like Apache or Nginx) involves downloading and configuring a bunch of things, so Mike recommends the `php -S localhost:4000` route for now.
- **Leave the server running.** Don't exit that window — just minimize it. If you close it, your site stops working.

### Step 2 — Create a workspace folder and your first PHP file

1. In the file explorer, go to the **document root** (in the video: `Users → Mike D`).
2. Create a **new folder** and name it `www`. ("`www` is just gonna stand for like our website — you can name it whatever you want.")
3. Open your text editor (Atom in the video — any editor works). Use **File → Add Project Folder** and select the `www` folder so you can see it while you develop.
4. **Right-click → New File** and create a file named **`site.php`**. You can name it whatever you want, but it **needs the `.php` extension**.

### Step 3 — Understand the relationship between PHP and HTML

- PHP is **very tightly coupled with HTML** — HTML stands for **Hypertext Markup Language**, "basically a language that we can use to build websites." If you've built a website before, you've used HTML.
- Mike assumes you have a **basic knowledge of HTML** — you don't need to be an expert, but you should understand what HTML is, how it works, and what's going on. (Giraffe Academy has a separate full HTML course if you need it.)
- **A PHP file is very similar to an HTML file.** You can write HTML inside a `.php` file and it works just like a normal HTML file. "For all intents and purposes, a PHP file and an HTML file are the same. It's just that in a PHP file, we can write PHP code."

### Step 4 — Write the code

First set up a basic HTML skeleton in `site.php`:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mike's Site</title>
</head>
<body>
    <?php
        echo "Hello World";
    ?>
</body>
</html>
```

Everything in HTML is a **tag**, and we can create a special **PHP tag**:

- `<?php` opens the PHP block (a less-than sign, a question mark, then `php`).
- `?>` closes it.
- **Anything inside these tags is considered PHP code.**

The one PHP instruction in the video:

```php
echo "Hello World";
```

- `echo` is a command that **prints something out onto the screen**.
- The `echo` is followed by open/close parentheses and open/close quotation marks, then the text `Hello World`, then a semicolon.
- "Anytime we write a line of code in PHP, we want to make sure that we include that semicolon."

### Step 5 — Run it in the browser

1. Save the file.
2. In the browser address bar type the server address (from the still-running terminal): `http://localhost:4000`.
3. Hitting Enter gives **"Not found"** — because there are no PHP files at the root of the document root.
4. Remember your file lives in the `www` folder, so navigate to it:

```
http://localhost:4000/www/site.php
```

"Now we're navigating to the folder where we're storing all of our files, and then I'm typing in the file name."

5. The page prints **Hello World**. "We have successfully run our first PHP program, we have everything set up, and we're ready to start working some more."

## Key metaphor(s)

- **localhost** = "the web address of your local computer."
- **Document root** = "where PHP is going to start looking for files that we're going to run."
- A PHP file and an HTML file are **"the same for all intents and purposes"** — except that in a PHP file you can write PHP code.

## Gotchas

- **Never close the server terminal.** If it's closed (or you exit it), the page stops working. Minimize it, don't exit it.
- **The `.php` extension is mandatory.** A file named `site.txt` or `site.html` won't be treated as PHP.
- **Forgetting the semicolon = error.** Every PHP instruction ends with one.
- The URL path in the browser must match the folder/file structure exactly: `http://localhost:4000/www/site.php`, not just `http://localhost:4000`.
- If you open `site.php` by double-clicking it in the file explorer, the browser will *show* the PHP source or download it — PHP only executes when served by the PHP server.

## Modern note

The built-in development server still ships with PHP 8.5 — `php -S localhost:4000` works exactly as in the video. Two modern conveniences:

- You can run a quick one-liner without any server at all:

```bash
php -r 'echo "Hello World";'
```

- The **short echo tag** `<?= ... ?>` is enabled by default in modern PHP and is equivalent to `<?php echo ... ?>`.

## Checkpoint

1. What command starts PHP's built-in web server, and what do the two parts after the command mean?
2. What is the "document root"?
3. What do `<?php` and `?>` do?
4. What does `echo` do, and what character ends every statement?
5. Build it yourself: create `www/site.php` with the HTML skeleton and `echo "Hello World";`, run `php -S localhost:4000`, and confirm the page renders in the browser at `http://localhost:4000/www/site.php`.

[← Chapter 03 — Choosing a Text Editor](03-choosing-a-text-editor.md) | [Course Map](../README.md) | [Chapter 05 — Writing HTML →](05-writing-html.md)
