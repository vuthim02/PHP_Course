# Projects — exactly as the video teaches them

These files reproduce the video's forms and PHP **exactly** as Mike writes them in
`site.php` (chapter 4). Run them with the built-in PHP server:

```bash
php -S localhost:4000
```

Then open in your browser:

- http://localhost:4000/projects/01-basic-calculator.php
- http://localhost:4000/projects/02-mad-libs-game.php
- ...etc

| File | Video chapter | What it does |
|---|---|---|
| `01-basic-calculator.php` | 11 | Adds two numbers from a form |
| `02-mad-libs-game.php` | 12 | Builds a poem from three words |
| `03-url-parameters.php` | 13 | Reads values straight from the URL |
| `04-post-vs-get.php` | 14 | Password field with POST |
| `05-checkboxes.php` | 16 | Multiple-choice fruit picker |
| `06-grade-lookup.php` | 17 | Looks up a student's grade by name |
| `07-better-calculator.php` | 22 | Add/subtract/multiply/divide with an operator |
| `08-grade-switch.php` | 23 | Grades via switch statement |

Run from the command line too (CLI-only parts print, form pages just show the form):

```bash
php -S localhost:4000 -t .
```
