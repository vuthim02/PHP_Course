# Chapter 29 — Classes & Objects

**Video section 29 (3:45:57)**

## What the video teaches

### The problem: built-in data types are too limited

PHP gives us a set of built-in data types:

- **strings** — just plain text
- **whole numbers** (integers) and **decimal numbers** (floats)
- **booleans** — true/false values

Some things model easily: someone's age, for example, can be a simple number (`$age = 90;`). But "a lot of times when we're writing our PHP programs, we're not going to be able to represent everything just using a single string, or a single boolean, or a single number." Real-world entities can't be broken down into a single string or number — and since PHP only has these few data types, "the types of information that we can represent and model in our programs is very limited."

PHP recognizes this problem, so it lets us create our **own custom data types** — by creating things called **classes**. A class "is essentially just a specification for a custom data type." Just as `string`, `integer`, and `boolean` are data types, you can create a custom one to model anything real: a phone, a keyboard, a water bottle — "anything I can think of in the real world, using a class."

### Scenario: software for a library

For this tutorial we're writing software that will help a library manage all of its books — so we create a **Book** class that lets us represent and model books inside the PHP program.

### Creating the Book class

```php
<?php
class Book {
    var $title;
    var $author;
    var $pages;
}
?>
```

Step by step, exactly as shown in the video:

1. Type `class`, a space, then the name of the class: `Book`. Convention: "a lot of times when we're creating classes, people will use a capital letter — it's not necessary, but that's just kind of a common convention."
2. Add an opening and closing curly brace `{ }`, and press Enter a few times — the class body goes inside those braces.
3. Break the book down into a series of **attributes**. "When we create a new data type, generally what we're going to do is create the new data type based off of those other data types." A good set of attributes for a book: a **title**, an **author**, and a **number of pages**. "Every book has a title. Every book has an author. And every book has a number of pages."
4. Declare each attribute with `var`, a dollar sign, the attribute name, and a semicolon: `var $title;`, `var $author;`, `var $pages;`.

"Essentially, what I'm doing here is telling PHP that I want to create a new Book data type, and this Book data type is going to be composed of a title, an author, and pages. So every book inside of our program will have a title, an author, and pages."

The class is a **specification** — "like a blueprint for creating a book inside of my program."

### Creating a Book object

Below the class declaration, create an actual book:

```php
<?php
$book1 = new Book;
$book1->title  = "Harry Potter";
$book1->author = "JK Rowling";
$book1->pages  = 400;

echo $book1->title;    // Harry Potter
echo $book1->author;   // JK Rowling
?>
```

- `new Book` creates a new Book data type and stores it inside the `$book1` variable. This is what we call an **object** — "an object is an instance of a class." Up top, the Book class is the blueprint; down here, we're creating an actual book.
- Because this is a real book, we can give it its own title, author, and number of pages. To access an object's attributes we use the **arrow operator** — a dash followed by a greater-than sign, `->`: `$book1->title`, `$book1->author`, `$book1->pages`.
- `echo $book1->title;` prints **Harry Potter**; `echo $book1->author;` prints **JK Rowling**.

"Before I created that Book class, I had no way of representing a book — no way of storing, representing, or modeling a book inside of my program. But now, since I created this Book class, I basically created a template for what a book is, I created a Book data type, and now I can use that Book data type to create variables. So now this `$book1` variable is actually storing a book object."

### A second object

Copy the whole block and make a second, different book:

```php
<?php
$book2 = new Book;
$book2->title  = "Lord of the Rings";
$book2->author = "Tolkien";
$book2->pages  = 700;

echo $book2->author;   // Tolkien
?>
```

- Change the variable from `$book1` to `$book2`, keep `new Book`, and give it different values: title "Lord of the Rings", author "Tolkien", 700 pages.
- Just like you can create two strings in your program, you can create two books.
- Now two Book objects exist: book1 = Harry Potter / JK Rowling / 400 pages; book2 = Lord of the Rings / Tolkien / 700 pages. "They're both books, but they have different titles, they have different authors, and they have different pages."
- `echo $book2->author;` prints **Tolkien**.

### The key distinction

- **Class** = the blueprint, the specification for what a book *is* in the program. It defines the new data type.
- **Object** = an instance of the class — an actual book with concrete values (a title, an author, a page count).

"And that is the beauty of classes and objects: we can take something complex like a book, and we can represent it inside of our programs."

## Key metaphor(s)

- **Blueprint vs. house**: "A class is a blueprint, a template for what a book is"; an object is "an instance of a class" — the actual book built from that blueprint.
- **Strings, times two**: creating two books works "just like I can create two strings" — the custom type behaves like any other type.
- **Custom data type**: building a class is like inventing a brand-new type (a "phone" type, a "water bottle" type) on top of PHP's built-in types.

## Gotchas

- Class names use a capital letter by convention (`Book`, not `book`) — the video notes it's not required, but it's standard.
- Without a class there is **no way** to represent a book in your program — that limitation is the entire motivation for custom data types.
- `var $title;` only *declares* the attribute; each object gets its own value via `$object->attribute = value;` before you read it.
- The arrow `->` (dash + greater-than sign) accesses an object's attributes — note it is not a period like in some other languages.

## Modern note

The `var` keyword works (it's an alias of `public`) but is legacy. Modern PHP 8 uses **typed properties**:

```php
<?php
class Book {
    public string $title;
    public string $author;
    public int $pages;
}
?>
```

Typed properties tell PHP exactly what each attribute holds and enforce it at runtime. Full modern OOP (visibility, typing, inheritance, interfaces) is covered in Level 2 of `php-mastery-course` (chapter 01-oop-fundamentals).

## Checkpoint

1. In your own words: what is a class, and what is an object?
2. What operator reads an object's property? How do you write it?
3. Define a `Car` class with three attributes, create two car objects, set their attributes, and echo them.
4. Why would PHP need classes if it already has strings, numbers, and booleans?
5. Run `php demos/classes-objects.php` and check that both books' properties print.

[← Chapter 28 — Include: PHP](28-include-php.md) | [Course Map](../README.md) | [Chapter 30 — Constructors →](30-constructors.md)
