<?php
// public/router.php — pretty URLs for the PHP built-in dev server.
// Run:  php -S localhost:4000 -t public router.php
//
// Real files (css, js, images) are served directly; everything else is handed
// to the front controller so URLs like /product/wireless-headphones work.

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== "/" && is_file($file)) {
    return false; // let the built-in server serve the file itself
}

require __DIR__ . "/index.php";
