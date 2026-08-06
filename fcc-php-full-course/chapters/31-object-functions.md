# Chapter 31 — Object Functions

**Video section 31 (4:06:18)**

## What the video teaches

An **object function** (also called a *method*) is "basically just a function that we can define inside of a class. And then the different objects of that class can use that function." These are super useful: "a lot of times, we're going to want to create a bunch of these little object functions that will either tell us information about the current object, or modify the current object, or do something like that."

### The setup: a Student class

```php
<?php
class Student {
    var $name;
    var $major;
    var $gpa;

    function __construct($name, $major, $gpa) {
        $this->name  = $name;
        $this->major = $major;
        $this->gpa   = $gpa;
    }
}

$student1 = new Student("Jim", "Business", 2.8);
$student2 = new Student("Pam", "Art", 3.6);
?>
```

- The class models a student in the program: every student has a **name**, a **major**, and a **GPA**.
- The constructor takes name, major, and GPA and assigns them to the object's attributes ("down here, I assigned the name that gets passed in equal to the name of the actual object").
- Two students to work with:
  - **Jim** — Business major, **2.8** GPA.
  - **Pam** — Art major, **3.6** GPA.

### Writing an object function: hasHonors()

The program is for a college/university, and we want to easily figure out whether a particular student is on the honor roll. The rule: **a GPA of 3.5 or above** means honors.

```php
<?php
function hasHonors() {
    if ($this->gpa >= 3.5) {
        return "true";
    }
    return "false";
}
?>
```

Step by step:

1. Below the constructor, declare it just like any other function: `function hasHonors()`.
2. It returns a true or false value: true if the student has honors, false if not.
3. Inside, build a simple `if` statement: `if ($this->gpa >= 3.5)`. Access the calling object's GPA with `$this->gpa` — the `$this` keyword reaches into the current object's attributes, exactly like `$this->title` in the constructor.
4. `return "true";` when the condition holds; `return "false";` otherwise.

A note from the video about booleans: "Whenever we're printing out true or false values, PHP isn't actually going to be able to print out false, and it's not gonna be able to print out true either." So the video returns **strings** `"true"` / `"false"` instead of real booleans — purely so the output is visible on screen. "You most likely would want to be passing back booleans" in real code.

### Using the function on each object

```php
<?php
echo $student1->hasHonors();    // false
echo $student2->hasHonors();    // true
?>
```

- `echo $student1->hasHonors();` calls `hasHonors()` *on* student1 → prints **false**, because Jim's GPA is 2.8, which is not 3.5 or greater.
- Change it to `echo $student2->hasHonors();` → prints **true**, because Pam's GPA is 3.6, which is above 3.5.
- (In the video, the first attempt hits a missing semicolon and the script errors — always terminate statements with `;`.)

### Why object functions are powerful

- "I wrote this function **one time**, and I was able to use it on the Pam object, and I was also able to use it on the Jim object." Every instance of the Student class can use `hasHonors()`.
- `$this->gpa` is dynamic: "When I call this function on the Pam object, when I say `$this->gpa`, that means we're going to use Pam's GPA. When I call the hasHonors function on the Jim object, again, this is going to use Jim's GPA. So by saying `$this->gpa`, this is always going to refer to the GPA of the object that's calling the function. And that's why these are so powerful."
- One definition, different answers per object.
- The rules are easy to change: lower the threshold from 3.5 to 2.5 and Jim would now qualify; raise it to 4.0 (or 4.5) and only exceptional students qualify. The function "essentially specifies the qualifications for honors."
- Takeaway advice: "Whenever you create a class in PHP, you always want to think about what are the different object functions that you can include inside of that class."

## Key metaphor(s)

- **Object = person, function = capability**: the Student class defines what a student *can do*; each specific student (Jim, Pam) uses that capability with *their own* data.
- **$this = "the current object"**: inside the method, `$this->gpa` means "the GPA of the object that's calling the function."

## Gotchas

- `$this->` is how a method reaches its own object's attributes (`$this->gpa`, `$this->name`, ...).
- `echo` can't display raw booleans — the video returns the strings `"true"` / `"false"` for visible output. In real code, return an actual boolean and format it when printing.
- The function only works on objects of the class it's defined in — `$student1->hasHonors()` is valid, but you can't call it on a plain string or number.
- Missing semicolons are easy to slip past in the editor — the video itself forgot one and PHP errored out on refresh.

## Modern note

Return a real `bool` with a return type declaration, and let the comparison be the return value:

```php
<?php
function hasHonors(): bool {
    return $this->gpa >= 3.5;
}

echo $student2->hasHonors() ? "true" : "false";
?>
```

Modern PHP would also type the properties (`public float $gpa;`) and the constructor parameters (`public function __construct(string $name, string $major, float $gpa) { ... }`).

## Checkpoint

1. What is `$this` inside a method, and why does the same `hasHonors()` give different answers for Jim and Pam?
2. Add a `hasPassed()` method to `Student` (e.g. gpa >= 2.0) and test both students.
3. Why did the video return `"true"` as a string instead of a boolean, and how would you do it properly?
4. What's one other object function a `Student` class might need? Write it.
5. Run `php demos/object-functions.php` and confirm it prints false then true.

[← Chapter 30 — Constructors](30-constructors.md) | [Course Map](../README.md) | [Chapter 32 — Getters & Setters →](32-getters-and-setters.md)
