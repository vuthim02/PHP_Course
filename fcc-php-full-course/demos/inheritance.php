<?php
// Chapter 33 — Inheritance (video 4:29:17)
class Chef {
    function makeChicken() {
        echo "The chef makes chicken<br>";
    }
    function makeSalad() {
        echo "The chef makes salad<br>";
    }
    function makeSpecialDish() {
        echo "The chef makes bbq ribs<br>";
    }
}

class ItalianChef extends Chef {
    function makePasta() {
        echo "The chef makes pasta<br>";
    }
    function makeSpecialDish() {
        echo "The chef makes chicken parm<br>";
    }
}

$chef = new Chef();
$chef->makeChicken();

$italianChef = new ItalianChef();
$italianChef->makeChicken();
$italianChef->makePasta();
$italianChef->makeSpecialDish();
$chef->makeSpecialDish();
?>
