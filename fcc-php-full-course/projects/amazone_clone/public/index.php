<?php
// public/index.php — the FRONT CONTROLLER.
// Every request that isn't a real file (css/js/img) lands here, and this file
// turns the URL into a controller action via routes.php.

declare(strict_types=1);

require __DIR__ . "/../app/bootstrap.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$path = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?? "/";
$path = rtrim($path, "/") ?: "/";

$routes = require __DIR__ . "/../app/routes.php";

$handled = false;

foreach (($routes[$method] ?? []) as $pattern => $target) {
    $regex = "#^"
        . str_replace(["{id}", "{slug}", "/"], ["(\d+)", "([a-z0-9-]+)", "/"], $pattern)
        . "$#";

    if (preg_match($regex, $path, $matches)) {
        array_shift($matches); // drop the full-match at index 0

        [$class, $action] = explode("@", $target);
        $controller = "App\\Controllers\\" . $class;

        (new $controller())->$action(...array_values($matches));
        $handled = true;
        break;
    }
}

if (!$handled) {
    http_response_code(404);
    echo "404 Not Found";
}
