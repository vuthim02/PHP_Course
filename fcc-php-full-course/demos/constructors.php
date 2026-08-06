<?php
// Chapter 30 — Constructors (video 3:56:23)
class Book {
    var $title;
    var $author;
    var $pages;

    function __construct($aTitle, $aAuthor, $aPages) {
        $this->title = $aTitle;
        $this->author = $aAuthor;
        $this->pages = $aPages;
    }
}

$book1 = new Book("Harry Potter", "JK Rowling", 400);
$book2 = new Book("Lord of the Rings", "Tolkien", 700);

echo $book1->title;
echo "<br>";
echo $book1->author;
echo "<br>";
echo $book1->pages;
echo "<br>";
echo $book2->author;
echo "<br>";

// Objects are still mutable after construction
$book1->title = "Hunger Games";
echo $book1->title;
?>
