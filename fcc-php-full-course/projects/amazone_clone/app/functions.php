<?php
// functions.php — global helpers used everywhere in the app.

// Escape output for HTML (kills XSS). Use on EVERY dynamic value in views.
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

// Read a dotted config key, e.g. config("site.currency").
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS["config"] ?? [];
    foreach (explode(".", $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

// Format a price like Amazon: $89.99 — symbol + whole + superscript cents.
function money(float|int|string $amount): string
{
    $parts = explode(".", number_format((float) $amount, 2));
    $symbol = config("site.currency", "$");

    return "<span class='amz-money'>"
        . "<span class='amz-money-symbol'>" . e($symbol) . "</span>"
        . e($parts[0])
        . "<span class='amz-money-cents'>." . e($parts[1] ?? "00") . "</span>"
        . "</span>";
}

// Render a 1-5 star rating (full + half + empty stars).
function stars(float $rating): string
{
    $full  = (int) floor($rating);
    $half  = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;

    $html  = "<span class='amz-stars' role='img' aria-label='" . e((string) $rating) . " out of 5 stars'>";
    $html .= str_repeat("<i class='full'>&#9733;</i>", $full);
    if ($half) {
        $html .= "<i class='half'>&#9733;</i>";
    }
    $html .= str_repeat("<i class='empty'>&#9734;</i>", $empty);
    return $html . "</span>";
}

// Send an HTTP redirect and stop.
function redirect(string $url): never
{
    header("Location: " . $url);
    exit;
}

// Current signed-in user summary, or null when browsing as a guest.
function current_user(): ?array
{
    if (empty($_SESSION["user_id"])) {
        return null;
    }
    return [
        "id"       => (int) $_SESSION["user_id"],
        "name"     => (string) ($_SESSION["user_name"] ?? ""),
        "email"    => (string) ($_SESSION["user_email"] ?? ""),
        "is_admin" => (bool) ($_SESSION["is_admin"] ?? false),
    ];
}

// Redirect to the sign-in page unless a user is signed in.
function require_auth(): void
{
    if (current_user() === null) {
        redirect("/login");
    }
}

// Redirect to sign-in, or block non-admins, for the admin panel.
function require_admin(): void
{
    $user = current_user();
    if ($user === null) {
        redirect("/login");
    }
    if (!$user["is_admin"]) {
        http_response_code(403);
        exit("Forbidden — admin access required.");
    }
}

// "Wireless Bluetooth Headphones!" -> "wireless-bluetooth-headphones"
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace("/[^a-z0-9]+/", "-", $text);
    return trim((string) $text, "-");
}

// Validate an uploaded image, save it under public/uploads/ with a random
// name, and return its public URL. Returns null when no file was uploaded.
// Throws RuntimeException on any validation/save problem.
function upload_image(?array $file): ?string
{
    if ($file === null || ($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Upload failed (error code " . (int) $file["error"] . ").");
    }
    if ($file["size"] > 2 * 1024 * 1024) {
        throw new RuntimeException("Image must be under 2 MB.");
    }

    $info  = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $info->file($file["tmp_name"]);
    $types = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp", "image/gif" => "gif"];
    if (!isset($types[$mime])) {
        throw new RuntimeException("Only JPG, PNG, WEBP or GIF images are allowed.");
    }

    $dir  = dirname(__DIR__) . "/public/uploads";
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = bin2hex(random_bytes(8)) . "." . $types[$mime];

    if (!move_uploaded_file($file["tmp_name"], $dir . "/" . $name)) {
        throw new RuntimeException("Could not save the uploaded image.");
    }
    return "/uploads/" . $name;
}

// True when the current URL path matches $path (ignores query strings).
// Used to highlight the link for the page you're on.
function is_active(string $path): bool
{
    $current = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?? "/";
    $current = rtrim($current, "/") ?: "/";
    $path = rtrim($path, "/") ?: "/";
    return $current === $path;
}

// Return (or create) the CSRF token for the current session.
function csrf_token(): string
{
    return $_SESSION["csrf_token"] ?? "";
}

// Output a hidden CSRF field for a form.
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// Verify the CSRF token on a POST. Call at the top of every POST action.
function csrf_verify(): void
{
    $sent = $_POST["csrf_token"] ?? "";
    if ($sent === "" || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit("Invalid CSRF token. Go back and try again.");
    }
}
