# Chapter 6: Generators

## Learning Objectives

- Create generators using `yield`
- Process large datasets efficiently
- Use generator delegation
- Understand memory advantages

---

## 6.1 Basic Generators

```php
<?php
// Simple generator
function rangeGenerator(int $start, int $end, int $step = 1): Generator
{
    for ($i = $start; $i <= $end; $i += $step) {
        yield $i;  // Pause execution and return $i
    }
}

foreach (rangeGenerator(1, 5) as $number) {
    echo $number; // 1 2 3 4 5
}

// Generator with keys
function fileLines(string $path): Generator
{
    $file = fopen($path, 'r');
    $lineNumber = 0;
    
    while (($line = fgets($file)) !== false) {
        yield ++$lineNumber => trim($line);
    }
    
    fclose($file);
}

foreach (fileLines('/var/log/app.log') as $lineNum => $line) {
    echo "[{$lineNum}] {$line}\n";
}
```

---

## 6.2 Memory Efficiency

```php
<?php
// ❌ Without generators — loads entire file into memory
function getLines(string $path): array
{
    return file($path, FILE_IGNORE_NEW_LINES);
}
// Memory: file size + array overhead

// ✅ With generators — reads one line at a time
function getLinesGenerator(string $path): Generator
{
    $handle = fopen($path, 'r');
    while (($line = fgets($handle)) !== false) {
        yield trim($line);
    }
    fclose($handle);
}
// Memory: ~8KB regardless of file size

// Processing large CSV
function processCsv(string $path): Generator
{
    $handle = fopen($path, 'r');
    $headers = fgetcsv($handle);
    
    while (($row = fgetcsv($handle)) !== false) {
        yield array_combine($headers, $row);
    }
    
    fclose($handle);
}

// Process millions of rows with constant memory
foreach (processCsv('users_10million.csv') as $user) {
    if ($user['country'] === 'US') {
        // Process one user at a time
        updateUserInDatabase($user);
    }
}
```

---

## 6.3 Generator Delegation

```php
<?php
function evenNumbers(int $max): Generator
{
    for ($i = 2; $i <= $max; $i += 2) {
        yield $i;
    }
}

function oddNumbers(int $max): Generator
{
    for ($i = 1; $i <= $max; $i += 2) {
        yield $i;
    }
}

// Delegate to sub-generators
function allNumbers(int $max): Generator
{
    yield 'odds' => oddNumbers($max);
    yield 'evens' => evenNumbers($max);
}

foreach (allNumbers(10) as $key => $value) {
    if ($value instanceof Generator) {
        echo "{$key}: ";
        foreach ($value as $num) {
            echo $num . ' ';
        }
        echo "\n";
    }
}
// odds: 1 3 5 7 9
// evens: 2 4 6 8 10

// yield from
function countdown(int $start): Generator
{
    if ($start > 0) {
        yield $start;
        yield from countdown($start - 1);
    }
}

foreach (countdown(5) as $num) {
    echo $num; // 5 4 3 2 1
}
```

---

## 6.4 Exercises

1. Create a generator that reads a large file line by line
2. Build a pagination generator that fetches pages from an API
3. Process a 1GB CSV file without loading it all into memory
4. Use generator delegation to merge multiple data sources

---

## Further Reading

- **Doc:** [PHP Generators](https://www.php.net/manual/en/language.generators.php)
