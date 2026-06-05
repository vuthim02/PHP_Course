# Project 3: Contact Form with Validation & CSRF Protection

A professional contact form with server-side validation, CSRF token protection, XSS-safe output, and JSON storage of submissions.

## Learning Objectives

- Form handling and POST processing
- Server-side validation (required fields, email format, min/max lengths)
- CSRF token generation and verification
- XSS prevention (`htmlspecialchars`)
- Session management for CSRF tokens
- JSON file storage
- Sticky form values (repopulating after error)
- Input sanitization and output encoding

## Features

- Fields: name, email, subject, message
- Validation: required checks, email format, min length (subject ≥ 3, message ≥ 10), max lengths
- Inline field-level error messages
- Sticky form data on validation failure
- CSRF token protection on every submission
- All output sanitized with `htmlspecialchars`
- Submissions saved to `submissions.json` with timestamp and IP
- Success message with option to send another

## How to Run

```bash
php -S localhost:8000 -t project-03-contact-form/
```

Then open `http://localhost:8000/index.php` in a browser.

## Code Structure

| File                | Purpose                                      |
|---------------------|----------------------------------------------|
| `index.php`         | Form display + validation logic in one file  |
| `submissions.json`  | Auto-created data file for entries           |
| `style.css`         | Styling with gradient theme                  |
| `README.md`         | This file                                    |

## PHP Concepts Practiced

| Concept                | Usage                                          |
|------------------------|------------------------------------------------|
| Superglobals           | `$_POST`, `$_SERVER`, `$_SESSION`              |
| Sessions               | CSRF token storage                             |
| Form handling          | Self-submitting POST form                      |
| Validation             | Required checks, `filter_var` with `FILTER_VALIDATE_EMAIL`, length checks |
| Sanitization           | `htmlspecialchars()` on all output             |
| Strings                | `trim`, `strlen`, `htmlspecialchars`        |
| Arrays                 | Compact, isset checks, associative arrays      |
| JSON                   | `json_encode` / `json_decode`                  |
| File operations        | `file_put_contents`, `file_get_contents`       |
| Date/time              | `date()` for timestamps                        |
| Error handling         | Inline error arrays, guard clauses             |
| Security               | CSRF tokens via `random_bytes` + `hash_equals` |
