<?php
// Chapter 26 — Comments (video 3:26:24)
// Comments are for humans, not the computer — PHP skips them.

// 1. Single-line comment: two forward slashes
// Everything after // on this line is ignored by PHP.

// 2. A comment that describes the code below it
// This line prints out a string:
echo "Hello<br>";

// 3. Inline comment after a line of code
echo "World<br>";   // anything after // on this line is a comment

// 4. Multiple single-line comments
// Comment on line one
// Comment on line two
// Comment on line three

// 5. Hash comments also work (same as //)
# This is a comment too, using the # symbol.
echo "After the hash comment<br>";

// 6. Comment block: /* ... */ can span any number of lines
/*
This whole block is a comment.
It can span as many lines as I want.
Nothing here gets rendered by PHP.
*/

// 7. Commenting out a line of code (temporary disable)
// echo "This line is commented out and won't run";
echo "Only this echo actually runs<br>";
?>
