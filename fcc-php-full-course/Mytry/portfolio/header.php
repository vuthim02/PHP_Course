<?php // header.php — shared top of every page (chapter 27: including HTML) ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $myName; ?> &mdash; <?php echo $myRole; ?></title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 0; color: #333; background: #f7f7f7; }
    nav { background: #222; padding: 14px 24px; }
    nav a { color: #ddd; text-decoration: none; margin-right: 18px; }
    nav a:hover { color: #fff; }
    main { max-width: 760px; margin: 40px auto; padding: 0 20px; }
    .card { background: #fff; border-radius: 8px; padding: 16px 20px; margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.1); }
    h1 { margin-top: 0; }
    a.project-link { color: #1d4ed8; }
    footer { text-align: center; color: #888; padding: 30px 0; font-size: .9em; }
  </style>
</head>
<body>
  <nav>
    <a href="index.php">Home</a>
    <a href="projects.php">Projects</a>
    <a href="skills.php">Skills</a>
    <a href="contact.php">Contact</a>
  </nav>
  <main>
