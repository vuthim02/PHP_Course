<?php
// Chapter 18 — Functions (video 2:04:55)
// A function is "a special container where we can put a bunch of code
// that's designed to perform a specific task."
// Run: php demos/functions.php   (or open it in the browser)

// In the video Mike evolves ONE sayHi() function through three versions,
// each replacing the previous one in site.php. We show all three side by
// side here (with distinct names) so the file runs from top to bottom.

// --- Version 1: no parameters — says hi to a generic user ---
function sayHi() {
    echo "Hello user<br>";
}

// --- Version 2: one parameter — the name ---
function sayHiTo($name) {
    echo "Hello $name<br>";
}

// --- Version 3: two parameters — name and age ---
function sayHiWithAge($name, $age) {
    echo "Hello $name, you are $age<br>";
}

// Defining a function alone prints NOTHING — you must call it.
sayHi();                        // Hello user

// Call it with different values to reuse the same code.
sayHiTo("Mike");                // Hello Mike
sayHiTo("Tom");                 // Hello Tom
sayHiTo("Dave");                // Hello Dave
sayHiTo("Oscar");               // Hello Oscar

// Pass as many parameters as you declare.
sayHiWithAge("Tom", 40);        // Hello Tom, you are 40
sayHiWithAge("Dave", 13);       // Hello Dave, you are 13
sayHiWithAge("Oscar", 80);      // Hello Oscar, you are 80
?>
