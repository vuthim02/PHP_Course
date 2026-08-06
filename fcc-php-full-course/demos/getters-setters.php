<?php
// Chapter 32 — Getters & Setters (video 4:13:52)
class Movie {
    public $title;
    private $rating;

    function __construct($title, $rating) {
        $this->title = $title;
        $this->setRating($rating);
    }

    function getTitle() {
        return $this->title;
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

$avengers = new Movie("Avengers", "PG-13");
echo $avengers->getRating();
echo "<br>";

$avengers->setRating("dog");
echo $avengers->getRating();
echo "<br>";

$avengers->setRating("R");
echo $avengers->getRating();
echo "<br>";

$dogMovie = new Movie("Dog", "Dog");
echo $dogMovie->getTitle();
echo "<br>";
echo $dogMovie->getRating();
?>
