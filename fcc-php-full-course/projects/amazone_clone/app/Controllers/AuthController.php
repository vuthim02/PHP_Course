<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

class AuthController
{
    public function registerForm(): void
    {
        if (current_user() !== null) {
            redirect("/");
        }
        $pageTitle = "Create Account";
        require __DIR__ . "/../views/auth/register.php";
    }

    public function register(): void
    {
        csrf_verify();

        $name     = trim((string) ($_POST["name"] ?? ""));
        $email    = trim((string) ($_POST["email"] ?? ""));
        $password = (string) ($_POST["password"] ?? "");
        $confirm  = (string) ($_POST["password_confirm"] ?? "");

        $fail = function (string $message) use ($name, $email): never {
            $_SESSION["flash"] = $message;
            $_SESSION["old"]   = ["name" => $name, "email" => $email];
            redirect("/register");
        };

        if ($name === "" || $email === "") {
            $fail("Please fill in every field.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail("That doesn't look like a valid email address.");
        }
        if (strlen($password) < 8) {
            $fail("Password must be at least 8 characters long.");
        }
        if ($password !== $confirm) {
            $fail("The two passwords do not match.");
        }
        if (User::findByEmail($email) !== null) {
            $fail("An account with that email already exists. Try signing in instead.");
        }

        $id = User::create($name, $email, $password);
        $this->signIn($id, $name, $email);
        $_SESSION["flash"] = "Welcome to amazone, " . $name . "!";
        redirect("/");
    }

    public function loginForm(): void
    {
        if (current_user() !== null) {
            redirect("/");
        }
        $pageTitle = "Sign in";
        require __DIR__ . "/../views/auth/login.php";
    }

    public function login(): void
    {
        csrf_verify();

        $email    = trim((string) ($_POST["email"] ?? ""));
        $password = (string) ($_POST["password"] ?? "");

        $user = User::verify($email, $password);
        if ($user === null) {
            $_SESSION["flash"] = "Incorrect email or password.";
            redirect("/login");
        }

        $this->signIn((int) $user["id"], $user["name"], $user["email"], (bool) $user["is_admin"]);
        redirect("/");
    }

    public function logout(): void
    {
        csrf_verify();

        $_SESSION = [];
        $p = session_get_cookie_params();
        setcookie(session_name(), "", time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        session_destroy();
        redirect("/");
    }

    private function signIn(int $id, string $name, string $email, bool $isAdmin = false): void
    {
        session_regenerate_id(true); // prevent session fixation
        $_SESSION["user_id"]    = $id;
        $_SESSION["user_name"]  = $name;
        $_SESSION["user_email"] = $email;
        $_SESSION["is_admin"]   = $isAdmin;
    }
}
