# Chapter 25 — For Loops

**Video section 25 (3:15:18)**

## What the video teaches

A **for loop** is "a special type of loop, which is used in conjunction with an **indexing variable**." While loops and do-while loops are awesome but *general* — they can handle any situation where you want to repeat a block of code. A for loop serves a more specific purpose: as you go through the loop, it **keeps track of an iterating variable** for you. That's what makes it special.

### The while-loop version, reconsidered

The previous tutorial left us counting to 5:

```php
<?php
$index = 1;

while ($index <= 5) {
    echo "while loop: $index<br>";
    $index++;
}
?>
```

The `$index` variable here is doing something important: "this variable `index` is actually keeping track of **how many times we've gone through the loop**." On the first iteration it's 1, on the second it's 2, on the third it's 3. That makes `$index` an **indexing / iterating variable** — "a variable that's changing every time we go through this loop." It could increment, decrement, or even add five each pass; the point is it changes.

Because counting with an index is "such a common and sought-after situation," PHP has a dedicated structure: the **for loop**.

### The for loop structure

```php
<?php
for ($i = 1; $i <= 5; $i++) {
    echo "for loop: $i<br>";
}
?>
```

The skeleton looks like a while loop (`for` + parentheses + curly brackets), but inside the parentheses we specify **three separate things**, separated by semicolons:

1. **Variable initialization** — `$i = 1;`
   - In the while-loop version we had to create `$index` *outside* the loop. In a for loop, "instead of having to place this outside of the loop, we can actually do it right here in the parentheses." Create the variable `$i` and give it the value `1`. This runs **once**, when the loop starts.
2. **The loop condition** — `$i <= 5;`
   - The same condition as the while loop, just using `$i` instead of `$index`.
3. **A line of code to execute after every iteration** — `$i++;`
   - "Essentially just a line of code that I want to execute after every iteration of the loop." Generally you modify the indexing variable here. `$i++` tells PHP to add one to `$i` at the end of every pass.

The for loop is **set up identically to the while loop**, and for all intents and purposes does exactly the same thing — the output is the same. The difference: "this while loop takes up 4 lines of code, this for loop really only takes up 2 lines of code." It's "way more compact, way more streamlined." And the benefit is that you now have this iterating variable you can modify and do whatever you want with.

### Modifying the iterating variable — counting by 2

Because the third slot runs after every iteration, you can change it however you like. `$i += 2` (the compound operator from chapter 6 — same as `$i = $i + 2`) makes the loop step by 2:

```php
<?php
for ($i = 1; $i <= 10; $i += 2) {
    echo $i . "<br>";
}
?>
```

Output: `1`, `3`, `5`, `7`, `9` — the loop starts at 1, runs the body, adds 2, checks `$i <= 10`, and repeats. "We could also decrement it, if we want, we could add five to it. It's basically just a variable that's changing every time we go through the loop."

### Looping through an array — the classic use

"Using this for loop, I want to actually show you guys how we can loop through the contents of an array. This is a very, very common use case for a for loop."

```php
<?php
$luckyNumbers = array(4, 8, 15, 16, 23, 42);

for ($i = 0; $i < count($luckyNumbers); $i++) {
    echo $luckyNumbers[$i] . "<br>";
}
?>
```

Line by line:

- `$luckyNumbers = array(4, 8, 15, 16, 23, 42);` — an array holding six numbers.
- **Start at `$i = 0`, not 1** — "that's because **array indexes start at zero**. The first element in the array is actually at index position 0." (`$luckyNumbers[0]` is the first element.)
- **Condition: `$i < count($luckyNumbers)`** — `count()` "tells me how many elements are inside this lucky numbers array," which is 6. So we loop while `$i` is *less than* the count.
- **Increment** `$i++` as usual.
- **Body: `echo $luckyNumbers[$i];`** — print "lucky numbers at index position `$i`."

What happens: first pass `$i = 0` → prints `luckyNumbers[0]` (4); second pass `$i = 1` → `luckyNumbers[1]` (8); and so on until the end.

### Why `<` and not `<=`

"Even though there's technically six elements inside of this array, the **index position of the last element is actually 5**" — `$luckyNumbers[5]` is the `42`. So the loop only needs to go up to index 5:

- `$i < count($luckyNumbers)` → `$i < 6` → stops after `$i = 5`. ✓
- If you used `<=`, the loop would also try `$luckyNumbers[6]`, which doesn't exist (index out of range).

So `for ($i = 0; $i < count($luckyNumbers); $i++)` prints all six numbers: `4`, `8`, `15`, `16`, `23`, `42`.

### The for-loop version of even numbers

The if-inside-the-loop trick from chapter 24 works the same way in a for loop — and the for loop bundles the counter so it's even tidier:

```php
<?php
for ($i = 1; $i <= 10; $i++) {
    if ($i % 2 == 0) {
        echo "even: $i<br>";
    }
}
?>
```

- `$i % 2 == 0` checks the remainder of `$i` divided by 2 (`%` is the modulo operator). Even numbers leave remainder 0.
- The loop supplies the counting (`$i++`) for you; the `if` filters the output.

Output: `even: 2`, `even: 4`, `even: 6`, `even: 8`, `even: 10`.

## Key metaphor(s)

> A for loop **packages all three pieces of a counting loop into one line**: initialize the index, state the condition, and update the index every pass. The while loop spreads the same work across four lines.

> The third slot is a running "do this after every lap" instruction — you can increment, decrement, or add five to the iterating variable.

## Gotchas

- **Array indexes start at 0**, so initialize `$i = 0` when walking an array.
- Use **`< count(...)`, not `<=`** — the last valid index is `count - 1`; `<=` runs one step past the end.
- Semicolons separate the three parts inside the parentheses — each part must be present (though parts can technically be empty).
- The initialization runs **once**; the condition runs **before each iteration**; the update runs **after each iteration** — in that order.

## Modern note

- For iterating arrays, **`foreach`** is the idiomatic modern tool (the video never covers it — worth learning now):

```php
<?php
foreach ($luckyNumbers as $num) {
    echo $num . "<br>";
}

foreach ($grades as $student => $grade) {
    echo "$student got $grade<br>";
}
?>
```

  - `foreach ($array as $value)` — plain values.
  - `foreach ($array as $key => $value)` — keys and values (great for associative arrays, chapter 17).
- Related loop controls: `break` exits a loop entirely; `continue` skips to the next iteration.
- The `for` loop still shines when you genuinely need the running index (e.g. writing `$array[$i]` side by side across the screen).

## Checkpoint

1. Name the three parts inside a `for` loop's parentheses and when each one runs.
2. Why does the array loop start at `$i = 0` and use `< count($luckyNumbers)` instead of `<=`?
3. What does `$i += 2` do, and what would the loop `for ($i = 1; $i <= 10; $i += 2)` print?
4. Print the numbers 1 to 10, then print every element of your own array using both `for` and `foreach`.
5. Write a for loop that prints only the even numbers from 1 to 10 using an `if` inside the loop.

[← Chapter 24 — While Loops](24-while-loops.md) | [Course Map](../README.md) | [Chapter 26 — Comments →](26-comments.md)
