# Level 1 — Practice Exercises

## Topic 1: Variables & Types

### Exercise 1.1 — Temperature Converter
Write a PHP script that converts Celsius to Fahrenheit. Store the Celsius value in a variable, compute `F = C * 9/5 + 32`, and output both values.

### Exercise 1.2 — Type Detective
Create variables of types: integer, float, string, boolean, array, and null. Use `var_dump()` and `gettype()` on each. Then cast a string `"42"` to int, a float `3.14` to string, and `"true"` to boolean — observe PHP's behavior.

### Exercise 1.3 — Variable Swap
Swap two variables `$a = 5` and `$b = 10` without using a third temporary variable.

---

## Topic 2: Strings

### Exercise 2.1 — String Analyzer
Write a script that:
- Takes a string `$text = "The quick brown fox jumps over the lazy dog"`
- Outputs its length, the number of words, and the position of the word "fox"
- Replaces "lazy" with "energetic"
- Converts everything to uppercase

### Exercise 2.2 — Name Formatter
Given `$fullName = "john  DOE"`, clean it up: trim extra spaces, capitalize the first letter of each word, and output `"John Doe"`.

### Exercise 2.3 — CSV Line Parser
Given a CSV line `$line = "Alice,30,alice@example.com"`, split it into an array using `explode()`, then format as an HTML table row string.

---

## Topic 3: Arrays

### Exercise 3.1 — Gradebook
```php
$grades = [
    'Alice' => [85, 92, 78],
    'Bob' => [70, 88, 95],
    'Charlie' => [90, 85, 89],
];
```
For each student, calculate the average grade. Output the class average. Find the student with the highest average.

### Exercise 3.2 — Shopping Cart
Simulate a shopping cart as an associative array:
- Add 3 items (name => price)
- Calculate total
- Apply a 10% discount
- Remove an item
- Output the final cart and total

### Exercise 3.3 — Array Operations
Start with `$numbers = range(1, 20)`. Without loops, use array functions to:
- Get only even numbers
- Square each number
- Get the sum of squares
- Find numbers divisible by 3

---

## Topic 4: Control Flow & Loops

### Exercise 4.1 — FizzBuzz
Print numbers 1 to 100. For multiples of 3, print "Fizz" instead. For multiples of 5, print "Buzz". For multiples of both, print "FizzBuzz".

### Exercise 4.2 — Prime Number Finder
Write a script that finds and prints all prime numbers between 1 and 100. Use a nested loop to check divisibility.

### Exercise 4.3 — Multiplication Table
Generate a 10×10 multiplication table. Use nested loops. Format the output so columns are aligned.

### Exercise 4.4 — Fibonacci Sequence
Print the first 20 numbers of the Fibonacci sequence (0, 1, 1, 2, 3, 5, 8...).

---

## Topic 5: Functions

### Exercise 5.1 — Palindrome Checker
Write a function `isPalindrome(string $word): bool` that returns true if the word reads the same forwards and backwards (case-insensitive, ignore spaces).

### Exercise 5.2 — Array Utility Functions
Implement three functions:
- `average(array $numbers): float` — calculates the mean
- `median(array $numbers): float` — finds the middle value after sorting
- `mode(array $numbers): array` — returns the most frequent values

### Exercise 5.3 — Calculator
Build a function `calculate(float $a, string $op, float $b): float` that supports `+`, `-`, `*`, `/`, `^` (power), `%` (modulo). Throw an `InvalidArgumentException` for division by zero.

### Exercise 5.4 — Recursive Directory Tree
Write a recursive function `listFiles(string $dir, string $prefix = "")` that prints a tree view of a directory and all its subdirectories.

---

## Topic 6: Forms & User Input

### Exercise 6.1 — Simple Calculator
Create an HTML form with two number inputs and a select dropdown (add, subtract, multiply, divide). POST to a PHP script that performs the calculation and displays the result.

### Exercise 6.2 — Comment Validator
Accept a comment form (name, email, comment). Validate:
- Name: required, max 50 chars
- Email: valid email format
- Comment: required, max 500 chars
Display errors inline if validation fails. Sanitize output with `htmlspecialchars()`.

---

## Topic 7: Sessions & Auth

### Exercise 7.1 — Login Counter
Create a simple login page (hard-coded username/password). When a user logs in, start a session and store `$_SESSION['visits']`. Each page load increments the counter. Display: "You've visited X times."

### Exercise 7.2 — Flash Messages
Implement a flash message system using sessions. When a user performs an action (e.g., saves data), store a success/error message in the session. Display it on the next page load, then clear it.

---

## Topic 8: File I/O

### Exercise 8.1 — Guestbook
Build a simple guestbook:
- A form to submit name + message
- Appends each entry as a line in `guestbook.txt`
- Displays all entries on the page
- Format: `"name||message||timestamp"` one per line

### Exercise 8.2 — Visitor Counter
Create a page that stores a visitor counter in a file. Increment the counter on each page load. Display: "You are visitor #42". (Beware of race conditions — show the naive approach first, then discuss flock.)

---

## Capstone Warm-Up: Contact Form

Build a complete contact form that:
1. Displays a form (name, email, subject, message)
2. Validates all fields server-side
3. Stores the submissions in a CSV file
4. Shows a success/error flash message
5. Lists all previous submissions at the bottom of the page
6. Has basic XSS protection (`htmlspecialchars` on output)
7. Is styled with minimal CSS

This exercise ties together forms, validation, sessions, file I/O, and output escaping.
