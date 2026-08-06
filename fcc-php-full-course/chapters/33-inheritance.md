# Chapter 33 — Inheritance

**Video section 33 (4:29:17)**

## What the video teaches

**Inheritance** is "where a class can inherit all of the functionality, all the attributes from another class." It's a really useful situation — and "the best way to kind of wrap your head around inheritance is just to see an example."

### The parent class: Chef

```php
<?php
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
?>
```

This class models a chef in the program. It's a very simple class: the chef can do three things:

- `makeChicken()` → prints "The chef makes chicken"
- `makeSalad()` → prints "The chef makes salad"
- `makeSpecialDish()` → prints "The chef makes bbq ribs" (barbecue ribs)

Outside the class, we create a chef and tell it to make chicken:

```php
<?php
$chef = new Chef();
$chef->makeChicken();       // The chef makes chicken
?>
```

Refreshing the browser shows "The chef makes chicken". Basic so far: a class, a bunch of functions, an object, and a method call.

### The child class: ItalianChef extends Chef

Now we want another chef. In addition to the generic chef, let's add an **Italian chef** — a more specialized type of chef. Without inheritance we'd create a second class and re-write all the same functions. But we want the Italian chef to be able to do *everything* the normal chef can do **plus a bunch of other stuff**. That's exactly the situation for inheritance.

```php
<?php
class ItalianChef extends Chef {
}
?>
```

- The magic is the **`extends`** keyword: `class ItalianChef extends Chef`.
- "Basically, what this means now is the Italian chef is going to be able to use all the same functions as the normal chef."

Create an Italian chef and use the inherited method:

```php
<?php
$italianChef = new ItalianChef();
$italianChef->makeChicken();    // The chef makes chicken
?>
```

"Up here in my ItalianChef class, I didn't actually define a makeChicken method — it's just not in here. But what I did was extend all the functionality from the Chef class, and the Chef class over here has a makeChicken function. So because the Chef has a makeChicken function, and I'm inheriting all the functionality from the Chef class, my ItalianChef is going to be able to make chicken, no problem."

Running the program: "The chef makes chicken" prints twice — once from the normal chef, once from the Italian chef. "Without having to write out the makeChicken function down here, I was still able to use it because of inheritance. And that's why inheritance is so cool."

### Adding new functionality: makePasta()

An Italian chef that only has the Chef class's functionality has no purpose. So add a brand-new method that only the Italian chef has:

```php
<?php
class ItalianChef extends Chef {
    function makePasta() {
        echo "The chef makes pasta<br>";
    }
}

$italianChef->makePasta();    // The chef makes pasta
?>
```

"In addition to making chicken, salad, and special dish, the Italian chef can also make a mean bowl of pasta."

But the *normal* chef cannot make pasta:

```php
<?php
$chef->makePasta();    // PHP Fatal error: Call to undefined method Chef::makePasta()
?>
```

"This normal chef isn't going to be able to make pasta... because the chef doesn't have a makePasta method. Only the Italian chef is capable of making pasta, because only the Italian chef has that function."

**The rule of thumb**: "Anytime we have two classes, like the Chef and the ItalianChef, where the ItalianChef can do everything the Chef can do plus some other stuff, we want to use inheritance. And this can be extremely powerful."

### Overriding a function

Have both chefs print their special dish:

```php
<?php
$chef->makeSpecialDish();        // The chef makes bbq ribs
$italianChef->makeSpecialDish(); // The chef makes bbq ribs (inherited)
?>
```

Both make bbq ribs, because ItalianChef inherited `makeSpecialDish()` from Chef. But the Italian chef doesn't want to make bbq ribs — it wants its own special dish. That's **overriding a function**: redefine the method in the child class using the **same name** as the parent's.

```php
<?php
class ItalianChef extends Chef {
    function makePasta() {
        echo "The chef makes pasta<br>";
    }

    function makeSpecialDish() {      // same name as Chef's version
        echo "The chef makes chicken parm<br>";
    }
}
?>
```

"It's actually really easy. All I have to do is come down here and say `function makeSpecialDish` — you'll notice I'm using the same name as the function that was up here in the Chef class — and then I can put my own thing: 'The chef makes chicken parm'."

Now each chef makes a *different* special dish:

```php
<?php
$chef->makeSpecialDish();        // The chef makes bbq ribs
$italianChef->makeSpecialDish(); // The chef makes chicken parm
?>
```

"Now the Italian chef makes chicken parm, and the normal chef makes bbq ribs. And that's what we would call overriding a function — we're overriding the makeSpecialDish function that we got from the Chef when we used inheritance. And that can come in handy all the time."

### The full example

```php
<?php
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
$chef->makeChicken();         // The chef makes chicken

$italianChef = new ItalianChef();
$italianChef->makeChicken();        // The chef makes chicken (inherited)
$italianChef->makePasta();          // The chef makes pasta
$italianChef->makeSpecialDish();    // The chef makes chicken parm (overridden)
$chef->makeSpecialDish();           // The chef makes bbq ribs
?>
```

### Closing thought

"That's sort of the basics of inheritance, and really the basics of using this `extends` keyword. As your PHP programs get more complex, and as you start using more and more complex classes, using something like inheritance can become very useful."

## Key metaphor(s)

- **Parent & child**: the Chef is the parent class; ItalianChef is a child that inherits everything the parent has. "The Italian chef can do everything the normal chef can do plus a bunch of other stuff."
- **Inheritance of a toolkit**: the child receives the parent's full toolkit (functions and attributes) for free, and may add its own tools or *re-tune* one of the inherited tools (override).

## Gotchas

- A child can call a method it never wrote, because it inherited it — `$italianChef->makeChicken()` works even though `ItalianChef` doesn't define `makeChicken`.
- The **parent cannot** use child-only methods: `$chef->makePasta()` is a fatal error ("Call to undefined method"), because Chef doesn't have `makePasta`.
- Overriding = redefining a same-named method in the child. Same name, new body; the child's version replaces the inherited one.
- Inheritance is one-directional: the child gains the parent's members, never the reverse.

## Modern note

- From inside the child you can call the parent's version with `parent::makeSpecialDish();`.
- **`protected`** is a third visibility level, between `public` and `private`: visible inside the class *and* its subclasses (useful for attributes/methods that children should see but the outside world shouldn't).
- **`final`** on a method stops it from being overridden; `final` on a class stops it from being extended.
- Modern design favors **composition and interfaces/traits** over deep inheritance chains for most cases (covered in `php-mastery-course` Level 2, chapters 03-inheritance and 04-traits-and-interfaces).

## Checkpoint

1. What does `extends` give a child class?
2. What is method overriding, and how does `$italianChef->makeSpecialDish()` prove it?
3. Why does `$chef->makePasta()` fail while `$italianChef->makePasta()` succeeds?
4. Create a `Vehicle` class with a `move()` method; extend it with `Car` (adds `honk()`) and override `move()` for `Bike`. Test all three objects.
5. Run `php demos/inheritance.php` and confirm each method prints the expected output.

[← Chapter 32 — Getters & Setters](32-getters-and-setters.md) | [Course Map](../README.md)
