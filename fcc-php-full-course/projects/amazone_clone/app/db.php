<?php
// db.php — one PDO connection, shared by every model.
// Default driver: SQLite (no server, no password). MySQL is supported too —
// just switch config "db.driver" to "mysql" and add host/name/user/pass.
// Prepared statements only — never build SQL by string concatenation.

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $db = config("db");

        if (($db["driver"] ?? "sqlite") === "sqlite") {
            $pdo = new PDO("sqlite:" . $db["path"]);
        } else {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $db["host"],
                $db["port"] ?? "3306",
                $db["name"],
                $db["charset"] ?? "utf8mb4"
            );
            $pdo = new PDO($dsn, $db["user"], $db["pass"]);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        if (($db["driver"] ?? "sqlite") === "sqlite") {
            $pdo->exec("PRAGMA foreign_keys = ON");
        }
    }

    return $pdo;
}
