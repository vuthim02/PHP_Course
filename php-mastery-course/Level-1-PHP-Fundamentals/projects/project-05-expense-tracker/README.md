# Project 5: Personal Expense Tracker

A CLI expense tracker to log, categorize, and analyze your spending. Data is stored in a JSON file and can be exported to CSV.

## Learning Objectives

- CLI argument parsing (`$argv`)
- Mathematical operations (sums, percentages, rounding)
- Arrays: grouping, sorting, filtering, `array_sum`, `arsort`
- Strings: `sprintf`, `number_format`, `implode`
- Date/time manipulation (`date`, `substr` for YYYY-MM)
- File I/O: JSON read/write, CSV export with `fputcsv`
- Functions with input validation
- Error handling for CLI usage
- Type declarations and strict types

## Features

- **Add** an expense with amount, category, and description
- **List** all expenses with running total
- **Totals** by category with percentage breakdown
- **Monthly** summary grouped by year-month (all or specific)
- **Export** all data to CSV file
- **Delete** an expense by ID
- Input validation for all parameters

## How to Run

```bash
php expense.php add 15.50 Food "Lunch at Subway"
php expense.php add 45.00 Transport "Gas station"
php expense.php add 800.00 Housing "Monthly rent"
php expense.php list
php expense.php totals
php expense.php monthly
php expense.php monthly 2025-03
php expense.php export
php expense.php delete 2
php expense.php help
```

## Code Structure

| File             | Purpose                                   |
|------------------|-------------------------------------------|
| `expense.php`    | CLI entry point — parses `$argv`          |
| `functions.php`  | All expense logic (CRUD, reports, export) |
| `expenses.json`  | Auto-created data storage                 |
| `README.md`      | This file                                 |

## PHP Concepts Practiced

| Concept                | Usage                                         |
|------------------------|-----------------------------------------------|
| CLI arguments          | `$argv` command routing                       |
| Math / numbers         | `round`, `array_sum`, `number_format`, percentages |
| Arrays                 | Associative arrays, `arsort`, `array_filter`, `array_column` |
| Strings                | `sprintf`, `printf`, `implode`, `number_format` |
| Date/time              | `date('Y-m-d')`, date grouping by substring   |
| JSON                   | `json_encode` / `json_decode`                 |
| File operations        | `file_get_contents`, `file_put_contents`, `fopen`, `fputcsv`, `fclose` |
| Functions              | Each feature as a named function              |
| Control flow           | `switch`, `foreach`, `if`/`else`              |
| Error handling         | Guard clauses, exit codes, validation         |
| Constants              | `define()` for data file path and categories  |
| Type declarations      | `declare(strict_types=1)`, typed parameters   |
