# Project 1: CLI Todo List Manager

A full-featured command-line todo list application written in PHP. Manage tasks directly from your terminal with persistent JSON storage.

## Learning Objectives

- PHP CLI (`$argv`, `PHP_SAPI`)
- File I/O (`file_get_contents`, `file_put_contents`)
- JSON encoding/decoding (`json_encode`, `json_decode`)
- Functions and includes/requires
- Arrays: filtering, mapping, column extraction
- String manipulation and formatting
- Control structures (`switch`, `foreach`)
- Error handling and edge case validation
- Type declarations (`declare(strict_types=1)`)

## Features

- **Add** a task with a description
- **List** all tasks with status, ID, and date
- **List filtered** by `done` or `pending`
- **Mark done** by task ID
- **Delete** by task ID
- **Search** tasks by keyword
- **Help** command shows usage

## How to Run

```bash
php todo.php add "Buy groceries"
php todo.php add "Walk the dog"
php todo.php list
php todo.php list pending
php todo.php done 1
php todo.php search groceries
php todo.php delete 2
php todo.php help
```

## Code Structure

| File           | Purpose                                  |
|----------------|------------------------------------------|
| `todo.php`     | CLI entry point — parses `$argv`         |
| `functions.php`| All task logic (CRUD, search, helpers)   |
| `tasks.json`   | Auto-created data storage                |
| `README.md`    | This file                                |

## PHP Concepts Practiced

| Concept                  | Usage                                       |
|--------------------------|---------------------------------------------|
| CLI arguments            | `$argv`, `$argc` to parse commands          |
| Functions                | All CRUD operations as named functions      |
| Includes                 | `require_once` to load `functions.php`      |
| Arrays                   | Indexed, associative, `array_filter`, `array_column` |
| Strings                  | `sprintf`, `strlen`, `strpos`, `substr` |
| JSON                     | `json_encode` / `json_decode` with flags    |
| File operations          | `file_get_contents`, `file_put_contents`    |
| Control flow             | `switch`, `foreach`, `if`/`else`            |
| Type declarations        | `declare(strict_types=1)`, typed returns    |
| Constants                | `define()` for the tasks file path          |
| Error handling           | Guard clauses, empty checks, exit codes     |
