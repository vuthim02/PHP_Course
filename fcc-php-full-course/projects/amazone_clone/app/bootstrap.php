<?php
// bootstrap.php — runs before every request.
// Loads helpers + DB, registers the autoloader, reads config, starts the session.

declare(strict_types=1);

require __DIR__ . "/functions.php";
require __DIR__ . "/db.php";

// Tiny PSR-4-style autoloader:  App\Controllers\HomeController
// resolves to  app/Controllers/HomeController.php
spl_autoload_register(function (string $class): void {
    $prefix = "App\\";
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $file = __DIR__ . "/" . str_replace("\\", "/", $relative) . ".php";
        if (is_file($file)) {
            require $file;
        }
    }
});

// Load config into a global so config() can read it.
$GLOBALS["config"] = require __DIR__ . "/config.php";

$sessionCfg = config("session");
session_name($sessionCfg["name"]);
session_set_cookie_params([
    "lifetime" => $sessionCfg["lifetime"],
    "httponly" => true,
    "samesite" => "Lax",
]);
session_start();

// Fresh CSRF token per browser session.
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
