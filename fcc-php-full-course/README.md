# PHP Full Course — From the freeCodeCamp Video (Mike Dane)

This course is written to **absolutely follow all 33 sections** of the video
**"PHP Programming Language Tutorial - Full Course"** by Mike Dane (Giraffe Academy),
published on freeCodeCamp.org (video ID `OK_JCtrrv-c`, 4h37m).

Every chapter below corresponds 1-to-1 with a timestamped section of the video.
Each chapter reproduces exactly what the video teaches — the concepts, the code,
the instructor's metaphors, and the gotchas — then adds a short "Modern PHP"
note so you can run the same ideas on PHP 8.5.

- **Reference video:** https://www.youtube.com/watch?v=OK_JCtrrv-c
- **Companion deep-notes file:** `../Video-Notes-Mike-Dane-PHP-Full-Course.md`
- **Our environment:** PHP 8.5.9 (Linux), CLI + built-in server `php -S localhost:4000`

---

## Course Map (exact video table of contents)

| # | Chapter file | Video timestamp | Topic |
|---|---|---|---|
| 01 | `01-introduction.md` | (0:00) | Introduction |
| 02 | `02-windows-installation.md` | (1:56) | Windows Installation |
| 03 | `03-choosing-a-text-editor.md` | (7:32) | Choosing a Text Editor |
| 04 | `04-hello-world-and-setup.md` | (11:06) | Hello World & Setup |
| 05 | `05-writing-html.md` | (20:29) | Writing HTML |
| 06 | `06-variables.md` | (27:30) | Variables |
| 07 | `07-data-types.md` | (38:09) | Data Types |
| 08 | `08-working-with-strings.md` | (44:27) | Working With Strings |
| 09 | `09-working-with-numbers.md` | (54:50) | Working With Numbers |
| 10 | `10-getting-user-input.md` | (1:05:14) | Getting User Input |
| 11 | `11-building-a-basic-calculator.md` | (1:15:37) | Building a Basic Calculator |
| 12 | `12-building-a-mad-libs-game.md` | (1:22:13) | Building a Mad Libs Game |
| 13 | `13-url-parameters.md` | (1:28:59) | URL Parameters |
| 14 | `14-post-vs-get.md` | (1:35:52) | POST vs GET |
| 15 | `15-arrays.md` | (1:41:44) | Arrays |
| 16 | `16-using-checkboxes.md` | (1:50:26) | Using Checkboxes |
| 17 | `17-associative-arrays.md` | (1:57:22) | Associative Arrays |
| 18 | `18-functions.md` | (2:04:55) | Functions |
| 19 | `19-return-statements.md` | (2:12:10) | Return Statements |
| 20 | `20-if-statements.md` | (2:19:10) | If Statements |
| 21 | `21-if-statements-cont.md` | (2:37:16) | If Statements (con't) — Comparisons |
| 22 | `22-building-a-better-calculator.md` | (2:47:13) | Building a Better Calculator |
| 23 | `23-switch-statements.md` | (2:56:53) | Switch Statements |
| 24 | `24-while-loops.md` | (3:05:09) | While Loops |
| 25 | `25-for-loops.md` | (3:15:18) | For Loops |
| 26 | `26-comments.md` | (3:26:24) | Comments |
| 27 | `27-including-html.md` | (3:31:08) | Including HTML |
| 28 | `28-include-php.md` | (3:36:51) | Include: PHP |
| 29 | `29-classes-and-objects.md` | (3:45:57) | Classes & Objects |
| 30 | `30-constructors.md` | (3:56:23) | Constructors |
| 31 | `31-object-functions.md` | (4:06:18) | Object Functions |
| 32 | `32-getters-and-setters.md` | (4:13:52) | Getters & Setters |
| 33 | `33-inheritance.md` | (4:29:17) | Inheritance |

---

## How to Use This Course

1. Watch the matching video section, then read the chapter and retype every example by hand.
2. Run each example: CLI (`php file.php`) or in the browser (`php -S localhost:4000` then open `http://localhost:4000/file.php`).
3. Do the "Checkpoint" at the end of every chapter before moving on.
4. Keep a `www/` folder as your workspace, exactly like the video sets up in chapter 4.

The chapters are written with modern PHP 8.5 in mind, so every example will run on
this machine's PHP 8.5.9 without changes.

## Runnable files

Everything the video types on screen is also saved as runnable files that reproduce
the code **exactly** as taught:

- `demos/` — every code demo from the video (hello world, variables, strings,
  numbers, arrays, functions, comparisons, loops, classes, constructors, object
  functions, getters/setters, inheritance, and the include examples).
  Run any of them with `php demos/hello-world.php`, or open them in the browser
  with the built-in server.
- `projects/` — the video's eight form projects (basic calculator, mad libs,
  URL parameters, POST vs GET, checkboxes, grade lookup, better calculator,
  grade switch). Run with `php -S localhost:4000` and open the URLs listed in
  `projects/README.md`.
