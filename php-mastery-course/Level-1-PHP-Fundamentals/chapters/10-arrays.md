# Chapter 10: Arrays

## Learning Objectives

- Create and manipulate indexed, associative, and multidimensional arrays
- Understand array operations and array functions
- Use array destructuring
- Master array iteration patterns
- Understand array performance characteristics

---

## 10.1 Array Types

```php
<?php
// Indexed array (ordered list)
$fruits = ['apple', 'banana', 'cherry'];
echo $fruits[0];  // 'apple'

// Associative array (key-value pairs)
$user = [
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'age' => 30,
];
echo $user['name'];  // 'Alice'

// Multidimensional array (array of arrays)
$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
    ['id' => 3, 'name' => 'Charlie'],
];
echo $users[1]['name'];  // 'Bob'

// Mixed array
$mixed = [1, 'hello', true, null, [1, 2]];
```

---

## 10.2 Array Operations

```php
<?php
// Creating arrays
$array = [1, 2, 3];
$array = array(1, 2, 3);  // Old syntax

// range() - create array of values
$numbers = range(1, 10);      // [1, 2, ..., 10]
$letters = range('a', 'z');   // ['a', 'b', ..., 'z']

// Adding elements
$array[] = 4;        // Append to end
array_push($array, 5, 6);
array_unshift($array, 0);  // Prepend to beginning

// Removing elements
$last = array_pop($array);      // Remove from end
$first = array_shift($array);   // Remove from beginning
unset($array[2]);               // Remove specific index

// Checking existence
var_dump(array_key_exists('name', $user));  // true
var_dump(isset($user['name']));             // true (not null)
var_dump(in_array('Alice', $users));        // false (array of arrays)
```

---

## 10.3 Array Destructuring

```php
<?php
// Indexed array destructuring
$array = [1, 2, 3];
[$a, $b, $c] = $array;
echo $a;  // 1
echo $b;  // 2
echo $c;  // 3

// Skip elements
[$first, , $third] = $array;

// Associative array destructuring
$user = ['name' => 'Alice', 'email' => 'alice@example.com'];
['name' => $name, 'email' => $email] = $user;

// In foreach
foreach ($users as ['id' => $id, 'name' => $name]) {
    echo "{$id}: {$name}\n";
}
```

---

## 10.4 Array Functions in Depth

```php
<?php
// Sorting
$fruits = ['banana', 'apple', 'cherry'];
sort($fruits);         // Values ascending: ['apple', 'banana', 'cherry']
rsort($fruits);        // Values descending
asort($fruits);        // Values ascending, preserve keys
ksort($fruits);        // Keys ascending

// Searching
$found = array_search('apple', $fruits);  // Returns key or false
$exists = in_array('apple', $fruits);     // Returns bool

// Slicing and splicing
$slice = array_slice($fruits, 1, 2);      // Extract portion
array_splice($fruits, 1, 2, ['date']);    // Replace portion

// Unique and merge
$unique = array_unique([1, 1, 2, 2, 3]);  // [1, 2, 3]
$merged = array_merge([1, 2], [3, 4]);    // [1, 2, 3, 4]

// Column extraction
$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
];
$names = array_column($users, 'name');    // ['Alice', 'Bob']

// Chunk
$chunks = array_chunk([1, 2, 3, 4], 2);  // [[1,2], [3,4]]

// Flip
$flipped = array_flip(['a', 'b', 'c']);  // ['a'=>0, 'b'=>1, 'c'=>2]
```

---

## 10.5 Exercises

1. Create an array of products with name, price, and category
2. Sort products by price descending
3. Group products by category
4. Calculate the total price of all products using array_reduce
5. Filter products under $10
6. Use array_column to extract all product names
7. Destructure a user array in a foreach loop

---

## Further Reading

- **Doc:** [Array Functions](https://www.php.net/manual/en/ref.array.php)
- **Doc:** [Array Destructuring](https://www.php.net/manual/en/migration71.new-features.php)
