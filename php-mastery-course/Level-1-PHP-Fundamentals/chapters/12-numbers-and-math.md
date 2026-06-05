# Chapter 12: Numbers and Math

## Learning Objectives

- Work with integers and floats
- Handle floating point precision
- Use math functions
- Generate random numbers
- Use BC Math for high precision

---

## 12.1 Integers

```php
<?php
// Integer literals
$a = 42;        // Decimal
$b = 0x2A;      // Hexadecimal (42)
$c = 0o52;      // Octal (42, PHP 8.1+)
$d = 0b101010;  // Binary (42)

// Integer overflow (64-bit systems)
echo PHP_INT_MAX;  // 9223372036854775807
echo PHP_INT_MIN;  // -9223372036854775808

// Overflow automatically becomes float
$large = PHP_INT_MAX + 1;
var_dump($large);  // float(9.2233720368548E+18)
```

---

## 12.2 Floats

```php
<?php
// Float literals
$a = 3.14;
$b = 1.2e3;    // 1200
$c = 7E-10;    // 0.0000000007

// NEVER compare floats directly for equality
$sum = 0.1 + 0.2;
var_dump($sum === 0.3);  // FALSE! (0.30000000000000004)

// Use epsilon comparison
$epsilon = 0.0000001;
var_dump(abs(($sum - 0.3)) < $epsilon);  // true

// Or round to needed precision
var_dump(round($sum, 2) === 0.3);  // true
```

---

## 12.3 BC Math (Arbitrary Precision)

```php
<?php
// Required for financial calculations, scientific computing
// Install: apt install php-bcmath

echo bcadd('0.1', '0.2', 2);      // "0.30"
echo bcsub('10.5', '3.2', 2);     // "7.30"
echo bcmul('2.5', '4.2', 2);      // "10.50"
echo bcdiv('10', '3', 4);         // "3.3333"
echo bcmod('10', '3');            // "1"
echo bcpow('2', '10');            // "1024"

// Financial calculation
function calculateInterest(string $principal, string $rate, string $time): string {
    return bcdiv(
        bcmul($principal, bcmul($rate, $time, 4), 2),
        '100',
        2
    );
}
```

---

## 12.4 Exercises

1. Calculate compound interest with monthly compounding
2. Round prices to 2 decimal places
3. Implement a random password generator
4. Fix floating point comparison for tax calculation
5. Use BC Math for currency operations

---

## Further Reading

- **Doc:** [Integer](https://www.php.net/manual/en/language.types.integer.php)
- **Doc:** [Float](https://www.php.net/manual/en/language.types.float.php)
- **Doc:** [BC Math](https://www.php.net/manual/en/book.bc.php)
