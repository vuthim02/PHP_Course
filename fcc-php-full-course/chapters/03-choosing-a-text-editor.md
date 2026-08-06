# Chapter 03 — Choosing a Text Editor

**Video section 3 (7:32)**

## What the video teaches

Mike talks about choosing the environment where you'll write your PHP code. He stresses right away that this is "one of the most important parts of the whole process" — but also that the decision is simple.

### The core message: any text editor works

- "With PHP, it's simple — **any text editor is going to work**. Any text editor where you can save a `.php` file is going to be able to support PHP."
- You don't need some special configuration, and you don't need some special text editor. "Any old text editor will do."

### Reuse your existing editor

- If you already have experience with something like **HTML, CSS, or JavaScript**, the text editor you use for those languages can be used for PHP.
- That's because you're generally going to be writing PHP **alongside** CSS, HTML, and JavaScript in the same project. "If you already have a text editor that you're comfortable with in those languages, then you can just use that same text editor."

### Your options, from simplest to specialized

If you're new to all this (or new to web development), Mike walks you through the range of choices:

- **The simplest:** Notepad (Windows) or TextEdit (Mac) — the plain text editors that come with your operating system. Really, that's all you *need*.
- **Specialized editors:** "A lot of people will like to use more of a specialized text editor — something that is designed to support the PHP language." These give you nice features:
  - **Syntax highlighting** — your code is colored so different parts (keywords, strings, variables) are visually distinct.
  - They can even **show you where the errors are in your code**.
- With a simple Google search you can find "a bunch of different text editors that are designed and support PHP."

### The editor Mike uses: Atom

- Mike uses a text editor he calls **Atom** — a text editor **created by GitHub** that he personally likes.
- He shows how to install it, in case you want to follow along with the same editor for the whole course:
  1. Go to the web browser and do a Google search for **"atom text editor"**.
  2. The site **atom.io** pops up — click it.
  3. Depending on your operating system, a download option appears (on Mac it says something like "Download OSX"; on Windows it's a Windows download).
  4. Click it — you get an installer file like **AtomSetup.exe**.
  5. Run it and wait for it to install. "Basically, all you need to do is wait for that to download, wait for it to install, and you can go ahead and start using it for the rest of this course."
- **But he's emphatic that you don't have to use Atom:** "Just because I'm using this Atom text editor doesn't mean that you have to... there's a lot of these different text editors out there."

### How to choose

- "The best text editor for you to use is going to be the one that you're most comfortable with."
- What you should do is find a text editor you'll be comfortable with **going forward**, especially if you're going to be writing a lot of code.
- Mike's practical tip: do a quick Google search — "look up what text editors seem to work for people with PHP" — and get an idea of what you can use.

## Key metaphor(s)

> "Whenever we're writing our PHP programs, one of the most important parts of the whole process is going to be the environment where we're writing our PHP code."

The "environment" here just means the editor window in front of you — not a complicated setup.

## Gotchas

- **Don't overthink this choice.** A plain editor is enough to learn PHP — the language doesn't care what you type into.
- Whatever you pick, **keep using the same editor through the course** so you don't get distracted switching tools.
- A specialized editor is a convenience (coloring, error hints), **not** a requirement for PHP to run.
- The `.php` file extension matters, not the editor: any editor that can save a file as `something.php` works.

## Modern note

**Atom has been retired** — GitHub stopped developing it (end-of-life December 2022), so atom.io no longer offers a maintained product. The modern community default is **VS Code** (free, from Microsoft) with the **PHP IntelliSense** extension, which gives you syntax highlighting, error highlighting, and autocomplete. Other popular choices are **PHPStorm** (paid, extremely full-featured) and **Sublime Text**. The lesson of the video is unchanged: **any text editor is fine** — what matters is that it can save `.php` files, and syntax highlighting makes life easier.

## Checkpoint

1. Do you need a special editor for PHP? Why or why not?
2. What two useful features does a specialized PHP editor add over Notepad/TextEdit?
3. What editor does Mike use, and is he requiring you to use it too?
4. Open one of the `.php` files from this course (`demos/hello-world.php`) in your editor and confirm the PHP code is syntax-highlighted. What colors does your editor use for `<?php`, `echo`, and the string?

[← Chapter 02 — Windows Installation](02-windows-installation.md) | [Course Map](../README.md) | [Chapter 04 — Hello World & Setup →](04-hello-world-and-setup.md)
