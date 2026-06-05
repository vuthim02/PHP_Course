# Chapter 4: Contributing to PHP

## Learning Objectives

- Understand PHP RFC process
- Submit patches to PHP core
- Propose new features
- Join the PHP community

---

## 4.1 RFC Process

```
1. Pre-Discussion (PHP Internals mailing list)
   ↓
2. Draft RFC (wiki.php.net)
   ↓
3. Discussion Period (2+ weeks)
   ↓
4. Vote (2/3 majority required)
   ↓
5. Implementation
   ↓
6. Merge to master
```

### Making Your First Contribution

```php
<?php
// 1. Clone PHP source
// git clone https://github.com/php/php-src.git
// cd php-src

// 2. Build PHP
// ./buildconf
// ./configure --disable-all --enable-debug
// make -j$(nproc)

// 3. Run tests
// make test

// 4. Fix a bug or add feature
// Check https://bugs.php.net for open bugs

// 5. Write test
// ext/standard/tests/array/array_map_variation.phpt
--TEST--
Test array_map() with closure accepting by reference
--FILE--
<?php
$array = [1, 2, 3];
$result = array_map(function(&$item) {
    return $item * 2;
}, $array);
var_dump($result);
?>
--EXPECT--
array(3) {
  [0]=>
  int(2)
  [1]=>
  int(4)
  [2]=>
  int(6)
}

// 6. Submit PR
// git checkout -b fix-array-map-ref
// git add ext/standard/tests/array/array_map_variation.phpt
// git commit -m "Fix array_map with by-reference callback"
// git push origin fix-array-map-ref
```

---

## 4.2 Exercises

1. Set up PHP source build environment
2. Find and fix a bug from bugs.php.net
3. Write a test for an existing PHP function
4. Submit a pull request to php-src

---

## Further Reading

- **Doc:** [Contributing to PHP](https://www.php.net/contributing.php)
- **Doc:** [PHP RFC Process](https://wiki.php.net/rfc/how-to-write)
