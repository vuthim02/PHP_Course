# Project 2: Guestbook with File Storage

A web-based guestbook application where visitors leave messages with their name and email. Entries are stored in a JSON file and displayed with the newest first. An admin panel allows deletion of entries.

## Learning Objectives

- HTML forms and POST handling
- Input validation and sanitization
- `htmlspecialchars` to prevent XSS
- JSON file storage (read/write)
- Array sorting with `usort`
- Superglobals (`$_POST`, `$_GET`, `$_SERVER`)
- HTTP redirects with `header()`
- Conditional rendering in views
- Date/time formatting

## Features

- Public form to sign the guestbook (name, email, message)
- Client-side validation (`required`, `type=email`) + server-side validation
- All output sanitized via `htmlspecialchars`
- Entries displayed newest-first with date
- Admin panel at `/admin.php` to view and delete entries
- Flash messages for success/error feedback

## How to Run

```bash
php -S localhost:8000 -t project-02-guestbook/
```

Then open `http://localhost:8000/index.php` in a browser.

## Code Structure

| File          | Purpose                                        |
|---------------|------------------------------------------------|
| `index.php`   | Main page — shows form and all entries         |
| `sign.php`    | Processes form submission, validates, saves    |
| `admin.php`   | Admin panel — lists entries with delete action |
| `style.css`   | Styling for all pages                          |
| `messages.json` | Auto-created data file                       |
| `README.md`   | This file                                      |

## PHP Concepts Practiced

| Concept                | Usage                                          |
|------------------------|------------------------------------------------|
| Superglobals           | `$_POST`, `$_GET`, `$_SERVER['REQUEST_METHOD']` |
| Form handling          | POST method, input trimming                    |
| Validation             | Required fields, email format via `filter_var` |
| Sanitization           | `htmlspecialchars()` for XSS prevention        |
| JSON                   | `json_encode` / `json_decode`                  |
| File operations        | `file_get_contents`, `file_put_contents`       |
| Arrays                 | `usort`, `array_filter`, `array_column`        |
| Date/time              | `date('Y-m-d H:i:s')`, `strtotime`             |
| Control flow           | `foreach`, `if`/`else`, loops in templates     |
| Includes/requires      | Functions duplicated across files (standalone) |
| Error handling         | Type checks, guard clauses                     |
