# Chapter 32 — Getters & Setters

**Video section 32 (4:13:52)**

## What the video teaches

Getters and setters are "special functions that we can create inside of our PHP classes, which allow us to control the access that people have to the attributes of those classes." That definition sounds confusing at first — this video shows exactly why they're useful and how to use them.

### The starting point: a Movie class

```php
<?php
class Movie {
    public $title;
    public $rating;

    function __construct($title, $rating) {
        $this->title  = $title;
        $this->rating = $rating;
    }
}

$avengers = new Movie("Avengers", "PG-13");
echo $avengers->rating;    // PG-13
?>
```

A movie has two attributes — a **title** and a **rating** — and a constructor to set them. A movie object is created (the Avengers, rated PG-13), and its rating is echoed "just to kind of prove that everything works."

### The problem: invalid ratings

We're designing a program that will store and work with a bunch of different movies. Every movie has a rating — PG-13, G, R, PG, and so on — but "there's really only a certain number of valid ratings for a movie": **G, PG, PG-13, R, and NR** (NR = "not rated").

The problem with the class as written: you can give a movie *any* rating you want:

```php
<?php
$avengers->rating = "dog";
echo $avengers->rating;    // dog
?>
```

"You were able to give this movie a rating that wasn't one of the valid ratings." For a simple toy class you might not care, but for a real application that stores and works with movies, you probably want to constrain the ratings — a movie rated "dog" is nonsense. "Up to this point, we haven't really talked about how we can control what values can be stored inside of these object attributes." That's exactly what getters and setters are for.

Goal: make it impossible for a movie to hold a rating other than G, PG, PG-13, R, or NR. "We're basically going to tighten down this class."

### Step 1 — Visibility modifiers: `public` vs `private`

```php
<?php
class Movie {
    public  $title;
    private $rating;
}
?>
```

A **visibility modifier** "is basically a keyword that's going to tell PHP what code is able to access and reuse different attributes in our programs." Two modifiers matter here:

- **`public`** — the attribute "is visible to any other code in my PHP program." It's open to everybody: anyone can access it, modify it, print it, do whatever they want.
  - This is essentially what the `var` keyword used in earlier lessons does. "Previously in this course, we were using the `var` keyword. I intentionally used `var` just because I didn't want to talk about these visibility modifiers until this tutorial... `var` and `public` for the most part are kind of interchangeable. Generally though, in modern day PHP, you're going to be seeing people use this `public` keyword."
- **`private`** — "any code outside of this Movie class... isn't going to be able to access the rating directly." Only code *inside* the class where the attribute is declared can use it.

Making `$rating` private cuts off direct access:

```php
<?php
echo $avengers->rating;          // PHP Fatal error:
                                 // Cannot access private property Movie::$rating
$avengers->rating = "dog";       // also impossible now
?>
```

Refreshing the page produces a **fatal error**: "Cannot access private property Movie::$rating" — PHP is telling us the movie's rating is private, so we can't access it anymore. Meanwhile the constructor still sets the rating fine, because that code lives *inside* the Movie class. Only code outside the class is locked out.

### Step 2 — The getter

```php
<?php
function getRating() {
    return $this->rating;
}
?>
```

A getter is a function that *returns* a private attribute. Now, instead of the illegal `$avengers->rating`, you write:

```php
<?php
echo $avengers->getRating();     // PG-13
?>
```

"Whenever somebody calls this getRating function, it's basically going to give them the rating." Even though the rating is private, we can now read it through the getter.

### Step 3 — The setter (and enforcing the rules)

```php
<?php
function setRating($rating) {
    if ($rating == "G" || $rating == "PG" || $rating == "PG-13" ||
        $rating == "R" || $rating == "NR") {
        $this->rating = $rating;
    } else {
        $this->rating = "NR";
    }
}
?>
```

A setter is a function that *sets* a private attribute — and this is where the rule lives:

1. It takes one parameter, the proposed rating.
2. An `if` statement checks whether it's one of the valid ratings, using a chain of `||` (OR) comparisons: G, PG, PG-13, R, or NR.
3. If it passes (it's valid), `$this->rating = $rating;` — set it. "If it passes this if statement condition, that means it's one of the valid ratings, so we can just go ahead and set it."
4. Otherwise, in the `else` block, `$this->rating = "NR";` — fall back to "not rated", because "they didn't give us a valid rating."

Mike's note on style: "This super long if statement... honestly there's easy ways that we could do something like this, but just for simplicity's sake, I wanted to do it this way." (The modern version later in this chapter shows a cleaner approach.)

Now even `$avengers->setRating("dog");` is filtered:

```php
<?php
$avengers->setRating("dog");
echo $avengers->getRating();     // NR

$avengers->setRating("R");
echo $avengers->getRating();     // R
?>
```

"Even though I passed in dog over here, when I print out the rating, it's going to be NR, because I passed in an invalid rating. It didn't make it through that if statement." A valid rating like "R" passes through the filter fine.

### Step 4 — Route the constructor through the setter too

There's one hole left: the constructor still assigns the rating directly, so `new Movie("Avengers", "Dog")` would bypass the validation entirely.

```php
<?php
function __construct($title, $rating) {
    $this->title = $title;
    $this->setRating($rating);    // route through the setter
}
?>
```

"Now every place where this rating gets set is going through this setRating [function]. So now we're not going to be able to get away with putting 'dog' inside of the constructor — now it's going to be set to 'not rated'."

"This attribute is officially locked down — we officially cannot give a movie an invalid rating. It's impossible for me to do that... even if I pass it into the constructor, or I come down here and say `$avengers->setRating` with some nonsense string, that's not going to work."

### The complete, locked-down class

```php
<?php
class Movie {
    public $title;
    private $rating;

    function __construct($title, $rating) {
        $this->title = $title;
        $this->setRating($rating);
    }

    function getRating() {
        return $this->rating;
    }

    function setRating($rating) {
        if ($rating == "G" || $rating == "PG" || $rating == "PG-13" ||
            $rating == "R" || $rating == "NR") {
            $this->rating = $rating;
        } else {
            $this->rating = "NR";
        }
    }
}
?>
```

### Recap of the whole mechanism (Mike's own summary)

- Made `$rating` **private** — nobody outside the class can touch it directly.
- Created a **getter** (`getRating()`) that returns the rating.
- Created a **setter** (`setRating()`) whose `if` statement filters out bad ratings and sets them to NR.
- In the **constructor**, called the setter so even creation-time values are validated.

### The important takeaway

- **`public`**: "the attribute is open to anybody, any program, anywhere."
- **`private`**: "only code inside the Movie class is going to be able to access this attribute."
- `var` (used in earlier lessons) is essentially `public`; "for the most part, people are only going to be using public and private."

## Key metaphor(s)

- **Bouncer / gatekeeper**: the setter is the single checkpoint every rating must pass through — valid ratings get in, nonsense ("dog") gets bounced and turned into NR.
- **Front desk / receptionist**: the getter is the front desk that hands out the private value to callers; the setter is the intake desk that inspects what comes in.
- **Private vault**: `private` is a locked vault inside the class — outside code can't even see it. Getters and setters are the two authorized doors (read = get, write = set), and the setter has the security guard (the `if` statement).

## Gotchas

- **Accessing a private property from outside the class is a fatal error**: `Cannot access private property Movie::$rating`. PHP stops the script.
- `public` = "open to everybody"; `private` = "only code inside the class it's declared in can use it."
- The constructor is code *inside* the class, so it CAN touch `private $rating` directly — but you should still route it through the setter, otherwise the validation has a back door.
- The setter's validation is case-sensitive and literal: `"PG-13"`, `"pg-13"`, or `"pg13"` are all treated as invalid and become NR. The `if` uses `==` against exact strings.
- `var` is effectively `public` — the video used `var` in earlier lessons only to defer the public/private discussion to this tutorial.

## Modern note

Modern PHP favors simple **typed public properties** over getter/setter boilerplate (especially `readonly` for immutable data). Getters/setters still shine when you need validation or logic. A cleaner modern version:

```php
<?php
class Movie {
    public function __construct(
        public string $title,
        private string $rating = "NR",
    ) {
        $this->rating = $this->sanitize($rating);
    }

    private function sanitize(string $rating): string {
        return in_array($rating, ["G", "PG", "PG-13", "R", "NR"], true)
            ? $rating : "NR";
    }

    public function rating(): string {
        return $this->rating;
    }
}
?>
```

Note how `in_array(..., true)` replaces the long `||` chain. And for a fixed set of values like G/PG/PG-13/R/NR, PHP 8.1 **enums** are the idiomatic modern answer.

## Checkpoint

1. What is the difference between `public` and `private`?
2. Why should the constructor call the setter instead of assigning `$rating` directly?
3. What happens — and what error do you get — if you try to read `$avengers->rating` directly after making it private?
4. Build a `BankAccount` class with a private balance, a getter, and a setter that refuses negative balances.
5. Run `php demos/getters-setters.php`: what does it print for a movie created with the invalid rating "Dog"?

[← Chapter 31 — Object Functions](31-object-functions.md) | [Course Map](../README.md) | [Chapter 33 — Inheritance →](33-inheritance.md)
