<?php
// data.php — ALL the info about you lives here. Edit this one file
// and every page of the site updates automatically.

// --- Basic info (chapter 6: variables) ---
$myName = "Your Name";
$myRole = "Web Developer";
$myEmail = "you@example.com";
$myLocation = "Your City, Country";
$myBio = "Hi! I am $myName. I am learning to build websites with PHP and "
    . "I love turning ideas into code. This is my little portfolio.";

// --- Skills: skill => skill level out of 5 (chapter 17: associative arrays) ---
$skills = array(
    "PHP"        => 4,
    "HTML / CSS" => 4,
    "JavaScript" => 3,
    "SQL"        => 2,
);

// --- Project class (chapters 29 & 30: classes & constructors) ---
class Project {
    public string $title;
    public string $description;
    public string $url;

    function __construct($aTitle, $aDescription, $aUrl) {
        $this->title = $aTitle;
        $this->description = $aDescription;
        $this->url = $aUrl;
    }
}

// --- Projects: an array of objects (chapter 15: arrays + chapter 30) ---
$projects = array(
    new Project("My First Website", "A tiny static page where I learned HTML.", "#"),
    new Project("Mad Libs Game",     "My first PHP program with a form and GET.", "#"),
    new Project("This Portfolio",    "Built with PHP includes, arrays and loops.", "#"),
);

// --- Helper function (chapter 18: functions) ---
// Turns a skill level (1-5) into dots: 5 ===> ooooo
function stars($level) {
    $dots = "";
    for ($i = 0; $i < 5; $i++) {
        $dots .= $i < $level ? "&#9679;" : "&#9675;";  // filled / empty dot
    }
    return $dots;
}
?>
