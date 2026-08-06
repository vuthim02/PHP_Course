# Chapter 18 — Functions

**Video section 18 (2:04:55)**

## What the video teaches

A **function** is "basically just a special container where we can put a bunch of code that's designed to perform a specific task."

The motivation: "a lot of times, when you're writing your PHP code, you're going to have certain code which is going to be naturally grouped together — certain code which is naturally just performing some common task. And a lot of times in PHP, what we can do is we can take code like that and we can put it inside of its own special container called a function."

Two big benefits:
- **Organization** — functions let you organize all the code on your website.
- **Reusability** — "a function is also going to be able to be reusable. So I can basically take some code that performs a specific task, put it inside of a function, and then I can use it in multiple places throughout my program. And that is extremely powerful."

### Defining a function

Mike creates a function whose whole purpose is to say hi to the user:

```php
<?php
function sayHi() {
    echo "Hello user<br>";
}
?>
```

- **`function`** — the keyword "is going to tell PHP that we want to create a function."
- **`sayHi`** — the name. "You basically want to give it a name which is going to describe what it's doing" — this one says hi to the user.
- **`( )`** — open and close parentheses.
- **`{ }`** — open and close curly brackets. "Any code that you put in between those curly brackets is going to be considered part of the function."
- Inside, the single line `echo "Hello user<br>";` — "I have one line of code here in my function, but you can have as many lines of code as you want. Functions can hold dozens or hundreds of lines of code — it doesn't really matter."

### Calling a function — the critical step

Mike refreshes the browser... and nothing happens. Nothing gets printed out. "Here's the problem: anytime we put code inside of a function like this, that code is only going to execute when we do something called **calling the function**. So for this code to execute, I have to call this function."

```php
<?php
function sayHi() {
    echo "Hello user<br>";
}

sayHi();
?>
```

- Below the function, type the function's name followed by open and close parentheses: `sayHi();`
- "Basically, what I'm doing here is I'm telling PHP that I want to execute all of the code inside of this function."
- "When PHP is looking through this file, and it comes down here and it sees `sayHi`, it's going to know that it has to jump up over here and execute this code."
- Refreshing now prints `Hello user` — "the code inside of that function is actually getting executed."

### Giving functions information — parameters

"We can take this a step further. Another cool thing we can do with these functions is we can actually give them information." That information is called **parameters** — "then the function can use those parameters, or the information that gets passed in, in order to do different things."

Instead of just saying hi to the user, make it say hi to someone specific — create a variable up in the parentheses:

```php
<?php
function sayHi($name) {
    echo "Hello $name<br>";
}

sayHi("Mike");
?>
```

- `function sayHi($name)` — a variable (`$name`) is declared inside the parentheses. "I specified that this function is going to take in a parameter — so it's going to take one value in. That means whenever I call this function, for example, if I call it down here, I have to pass it a name; I have to pass it a value."
- The echo prints `$name` instead of `user`.
- `sayHi("Mike")` — the value `Mike` is passed in. "Now this value is going to get stored inside of this `name` variable, and it's going to print out `Hello Mike` down here."
- Change the argument to `Tom` → prints `Hello Tom`. "So this function is using the piece of information that I gave it in order to perform its task a little bit differently."

### Reusing the function

"Another cool thing we can do with functions is we can actually reuse this code. So I can write this code up here one time, and I can execute it as many times as I want inside of my program."

```php
<?php
function sayHi($name) {
    echo "Hello $name<br>";
}

sayHi("Tom");
sayHi("Dave");
sayHi("Oscar");
?>
```

- Copy and paste the call a few times, passing different values each time.
- "I'm basically saying that I want to call this function three times, I'm passing it three different pieces of information." (Mike also puts a `<br>` break tag inside the echo "so we can kind of see this a little bit easier.")
- The page prints `Hello Tom`, `Hello Dave`, `Hello Oscar`.
- "So I wrote this code one time — I wrote the code to say hi to the user one single time — and I was able to use it three times throughout my program. And that's kind of one of the core concepts with functions: you can write them once, and you can use them a bunch of different times."

### Passing in as many parameters as you want

"In addition to just passing in one parameter, I could pass in as many as I want." Add a second parameter with a comma:

```php
<?php
function sayHi($name, $age) {
    echo "Hello $name, you are $age<br>";
}

sayHi("Tom", 40);
sayHi("Dave", 13);
sayHi("Oscar", 80);
?>
```

- `function sayHi($name, $age)` — two parameters, separated by a comma.
- `echo "Hello $name, you are $age<br>";` — both values are interpolated into the message.
- The calls now pass two arguments each: `sayHi("Tom", 40)`, `sayHi("Dave", 13)`, `sayHi("Oscar", 80)`.
- The page prints `Hello Tom, you are 40`, `Hello Dave, you are 13`, `Hello Oscar, you are 80`.
- "So we can pass in two or three — I mean, you can pass in as many parameters basically as you want. And then whenever you call the function, you need to pass them in, just like that."

### Wrap-up

"So that's why functions are useful — functions are actually extremely useful. And there's a lot of situations in PHP where we're going to want to use them." (Next up, chapter 19: return statements, where a function can also *give information back*.)

## Key metaphor(s)

> A function is "a special container where we can put a bunch of code designed to perform a specific task" — write it once, use it many times.

- Defining a function is like programming a kitchen appliance: nothing happens until you press the button (call it).
- Parameters are the slots you fill with ingredients each time you call it.

## Gotchas

- Defining a function produces **no output** — you must **call** it (`sayHi();`) for the code inside to run.
- The parentheses are required on both the definition and the call — `sayHi` alone doesn't call anything.
- Call arguments must be passed in the order the parameters are declared (`$name` first, then `$age`).
- If a function declares parameters, you must supply matching arguments when you call it.
- Code inside the curly brackets is the function; anything outside runs top-to-bottom as usual.

## Modern note

Add **types** to make functions self-documenting and safe:

```php
<?php
declare(strict_types=1);

function sayHi(string $name, int $age): void {
    echo "Hello $name, you are $age<br>";
}
?>
```

- `declare(strict_types=1)` makes type mismatches errors instead of silent conversions.
- `string $name, int $age` declare the parameter types.
- `: void` says the function returns nothing.
- **Named arguments** (PHP 8) let you pass arguments in any order by name: `sayHi(age: 40, name: "Tom")`.

## Checkpoint

1. What is the difference between defining a function and calling it?
2. What are parameters for?
3. Write a function that greets someone with their name and hometown, then call it three times with different people.
4. Why is defining a function *before* or *after* the calls often both fine, but the call itself is mandatory?

[← Chapter 17 — Associative Arrays](17-associative-arrays.md) | [Course Map](../README.md) | [Chapter 19 — Return Statements →](19-return-statements.md)
