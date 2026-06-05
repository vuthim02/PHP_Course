# Chapter 13: Date and Time

## Learning Objectives

- Use DateTime class and functions
- Format dates and times
- Handle timezones
- Calculate date intervals
- Parse date strings

---

## 13.1 DateTime Class

```php
<?php
// Creating DateTime objects
$now = new DateTime();                      // Current time
$specific = new DateTime('2024-01-15');     // Specific date
$specific = new DateTime('2024-01-15 10:30:00');
$specific = new DateTime('now', new DateTimeZone('UTC'));

// Formatting
echo $now->format('Y-m-d H:i:s');           // 2024-01-15 10:30:00
echo $now->format('l, F j, Y');            // Monday, January 15, 2024

// Modifying
$now->modify('+1 day');
$now->modify('first day of next month');
$now->modify('-2 weeks');

// Differences
$start = new DateTime('2024-01-01');
$end = new DateTime('2024-12-31');
$diff = $start->diff($end);
echo $diff->days;                           // 365
echo $diff->format('%y years %m months %d days');
```

---

## 13.2 Timestamps

```php
<?php
// Unix timestamp (seconds since Jan 1, 1970 UTC)
echo time();                    // Current timestamp
echo microtime(true);           // Microseconds (float)

// Timestamp to DateTime
$dt = new DateTime('@1705316400');  // From timestamp
$dt->setTimezone(new DateTimeZone('UTC'));

// DateTime to timestamp
echo $dt->getTimestamp();
```

---

## 13.3 Timezones

```php
<?php
// List all timezones
$timezones = DateTimeZone::listIdentifiers();

// Set timezone
date_default_timezone_set('UTC');

$utc = new DateTime('now', new DateTimeZone('UTC'));
$ny = clone $utc;
$ny->setTimezone(new DateTimeZone('America/New_York'));
$tokyo = clone $utc;
$tokyo->setTimezone(new DateTimeZone('Asia/Tokyo'));

echo "UTC: " . $utc->format('H:i') . "\n";
echo "NY:  " . $ny->format('H:i') . "\n";
echo "TKY: " . $tokyo->format('H:i') . "\n";
```

---

## 13.4 Exercises

1. Display the current date in multiple formats
2. Calculate the number of days until Christmas
3. Convert a time from UTC to multiple timezones
4. Parse "2024-01-15 10:30:00" into a DateTime and format it
5. Generate a list of dates for the next 7 days

---

## Further Reading

- **Doc:** [DateTime](https://www.php.net/manual/en/class.datetime.php)
- **Doc:** [DateTimeZone](https://www.php.net/manual/en/class.datetimezone.php)
- **Doc:** [Date Functions](https://www.php.net/manual/en/ref.datetime.php)
