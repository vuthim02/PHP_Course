# Chapter 30 — Constructors

**Video section 30 (3:56:23)**

## What the video teaches

A **constructor** is "basically just a special function that we can put inside of a class, which is going to get called when we create an object of that class." You can use constructors to do a bunch of cool stuff; this tutorial uses them to give objects their initial information automatically — "it will make it a lot easier for us to create objects in PHP."

### Recap: manual object creation is tedious

From the last tutorial: a Book class with `$title`, `$author`, and `$pages`, plus two objects created by hand:

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;
}

$book1 = new Book;
$book1->title  = "Harry Potter";
$book1->author = "JK Rowling";
$book1->pages  = 400;

$book2 = new Book;
$book2->title  = "Lord of the Rings";
$book2->author = "Tolkien";
$book2->pages  = 700;
?>
```

Creating one book took **4 lines of code** (create the object, then assign title, author, and pages individually); two books took **8 lines**. "Imagine if we had to create like 20 or 30 of these different books in our program — the amount of lines of code it would take would be ridiculous." The constructor fixes this.

### What a constructor is

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;

    function __construct() {
        echo "New Book Created<br>";
    }
}
?>
```

- Declared like any other function: `function` plus the name.
- The name is **two underscores** followed by `construct`: `__construct`. "It needs to be named `__construct`. If you don't name it exactly like that, then this isn't going to work."
- Inside the open/close curly braces you write whatever should happen whenever an object is created.
- Proof of concept: put `echo "New Book Created<br>";` in the constructor. Run the program → "New Book Created" prints **twice**, once for each `new Book` statement. "Whenever we create a new book object, this function gets executed."

### Constructors are normal functions — they take parameters

```php
<?php
function __construct($name) {
    echo "Hello $name<br>";
}

$book1 = new Book("Mike");
$book2 = new Book("Tom");
// prints: Hello Mike, then Hello Tom
?>
```

"This works just like any normal function — I could pass it some information." Whatever values you pass to `new Book(...)` get passed straight into `__construct(...)`. Run it and you print "Mike", then "Tom" — the two values passed into the constructor.

The relationship to remember: "Whenever I say `new Book` down here, when I save this, it's actually calling that `__construct` function. So that is extremely important."

### The real use: initializing the object

Pass in the book's title, author, and pages, and let the constructor assign them to the object's attributes:

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;

    function __construct($aTitle, $aAuthor, $aPages) {
        $this->title  = $aTitle;
        $this->author = $aAuthor;
        $this->pages  = $aPages;
    }
}

$book1 = new Book("Harry Potter", "JK Rowling", 400);
$book2 = new Book("Lord of the Rings", "Tolkien", 700);

echo $book1->title;    // Harry Potter
echo $book2->author;   // Tolkien
?>
```

Step by step:

1. The constructor takes three parameters: `$aTitle`, `$aAuthor`, `$aPages`. The little lowercase `a` prefix is not required — "I'm just doing that because it's going to be easier for us to see what's going on... we can name those whatever we want."
2. Inside, `$this->title = $aTitle;` assigns the passed-in value to the current object's title. Repeat for author and pages: `$this->author = $aAuthor;` and `$this->pages = $aPages;`.
3. **`$this`** is "actually a keyword in PHP, and it's going to refer to the current object" — the object that's being created. Compare `$this->title` inside the constructor with `$book1->title` outside: "these are doing the same thing." `$this` just stands in for whichever object is being created right now, so instead of saying `$book1`, we say `$this`.
4. Creating a book is now a single line: `new Book("Harry Potter", "JK Rowling", 400);`. The constructor does the four lines of setup for you. Two books went from **8 lines of code down to 2**.
5. Verify it works: `echo $book1->title;` → "Harry Potter". "This is working exactly like it worked before, it's just way easier for us to create these objects."

### You can still modify values after construction

```php
<?php
$book1->title = "Hunger Games";
echo $book1->title;    // Hunger Games
?>
```

"Instead of being Harry Potter, it's going to be Hunger Games. The whole point of using this constructor is that we can give this information right up front, so I don't have to manually set it — I can just do it right away, and the object has some initial information."

### Closing thought

"Constructors are extremely useful. A lot of times when people create classes, they'll create constructors for those classes."

## Key metaphor(s)

- **Handshake at birth**: `new Book(...)` automatically calls `__construct(...)`, like a handshake the moment an object is born — the constructor runs before you can do anything else with the object.
- **Prefilled form**: instead of filling out four fields by hand per book, the constructor is the prefill — one line supplies the title, author, and pages up front.
- **`$this` = "the current object"**: inside the constructor, `$this->title` means "the title of the object that's getting created" — the same as writing `$book1->title` outside.

## Gotchas

- The name must be exactly `__construct` (two underscores). Misspell it and PHP won't call it — "this isn't going to work."
- The constructor fires **every** time `new` runs — if you echo inside it, you'll see one output per object created.
- Parameters are positional: `new Book("Harry Potter", "JK Rowling", 400)` maps in order to `$aTitle`, `$aAuthor`, `$aPages`.
- Construction gives initial values, but objects are still mutable afterward — you can always reassign `$book1->title`.

## Modern note

PHP 8 **constructor property promotion** collapses the boilerplate further — attributes are declared right in the parameter list:

```php
<?php
class Book {
    public function __construct(
        public string $title,
        public string $author,
        public int $pages,
    ) {}
}

$book = new Book("Harry Potter", "JK Rowling", 400);
echo $book->title;
?>
```

Note modern constructors also get `string`/`int` type declarations on their parameters. If you don't define any `__construct`, PHP provides a default constructor that simply does nothing.

## Checkpoint

1. When does `__construct()` run, and how often?
2. What does `$this` refer to inside a constructor?
3. Why is the constructor faster than the manual approach from chapter 29? (Compare 4 lines vs 1 line per book.)
4. Rewrite your `Car` class from chapter 29 to use a constructor, then create two cars with one line each.
5. Predict what happens if you misspell `__construct`, then test it.
6. Run `php demos/constructors.php` and confirm the book properties — and the post-construction change to "Hunger Games" — print correctly.

[← Chapter 29 — Classes & Objects](29-classes-and-objects.md) | [Course Map](../README.md) | [Chapter 31 — Object Functions →](31-object-functions.md)
