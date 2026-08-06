# Chapter 14 — POST vs GET

**Video section 14 (1:35:52)**

## What the video teaches

Two different ways to get information from users — the two form methods **GET** and **POST**. Mike talks about "the difference between the two of them, and we'll have a look at what they're doing, and when you should use get, and when you should use post."

### The starting program — a password field

Down at the bottom Mike has a basic program set up. The form asks the user for a password:

```html
<form action="site.php" method="get">
    Password: <input type="password" name="password">
    <input type="submit">
</form>
```

```php
<?php
echo $_GET["password"];
?>
```

- `type="password"` — "whenever we put type password over here, this is actually going to give us a textbox which like is a password." When you type in it, "it sort of blocks it out" — you see dots instead of the characters. "That's basically just like you can see a lot on different websites, just so nobody can see what you're typing in when you're typing in your password."
- The name is `password`, and the PHP just echoes the password back so we can see what was entered.
- Entering a password and clicking Submit prints it below the form.

### How GET works

"Up to this point in this course, whenever we've been building these forms, we've always set this method equal to get. And basically, when we set this method equal to get, what's going to happen is the information that the user enters is obviously going to get submitted into PHP, and we're going to be able to use it — but that information is also going to get put up in the URL as a URL parameter."

Mike demonstrates: he enters the password `banana` and clicks Submit. Because of the `get` method, `password=banana` shows up right there in the URL — visible to anyone.

### Why GET is wrong for a password

"I'm sure you can imagine like this is not a good scenario for a password." Certain information is fine to show in the URL — "it's not going to be a problem... with certain pieces of information." But with a password, "if your password is actually showing up inside of the URL, that is extremely insecure."

Worse, he can edit it: he changes the password in the URL to `orange`, and "the password is now updated throughout the entire page." So "a piece of information like a password is not something that should be stored up there inside the URL, and it's not something that the user should just be able to change willy-nilly whenever they want in the URL."

### Switch to POST

"In a situation like that, where we have information that we want to pass between the client and the server more securely, we want to use the POST method." The change is tiny — just change the method, and the matching superglobal:

```html
<form action="site.php" method="post">
    Password: <input type="password" name="password">
    <input type="submit">
</form>
```

```php
<?php
echo $_POST["password"];
?>
```

- Change `method="get"` to `method="post"` in the form.
- Change `$_GET` to `$_POST` in the PHP — "so basically, this over here is going to match this down here."

"Post is basically just going to do exactly what get did, except it's going to do it without placing it up in the URL parameter. And there's also a couple other small differences — like when you use post, you can actually get potentially more information from the user than when you use get. But the main difference is that when we use post, it's going to be more secure, so that information isn't going to show up inside of the URL."

### Testing POST

Mike refreshes the page: even though `password=orange` is still sitting in the URL from before, "it's not showing up down here anymore" — that old URL value is ignored because the form now uses POST.

He types a new password into the text field and clicks Submit: "now we're going to be able to grab that information securely. And you'll notice up here in the URL, there's no information — that information did not show up in the URL. It got passed between the client and the server in a more secure fashion."

### GET vs POST — the summary

"That's basically the difference between GET and POST. Get is just kind of like anything goes — anyone can see the information, it's up there in the URL. And in a lot of cases, that's going to be useful. But in a lot of other cases — like in the case of a password, or even like a username or credit card number, I mean any type of secure information — you don't want it to show up inside that URL. You want it to be passed more securely back to PHP, and we can use post in order to do that."

"So like I said, in certain circumstances get is going to be appropriate, in certain circumstances post is going to be appropriate. But now that you kind of know the difference, you can kind of make that decision for yourself."

### The rule of thumb (Mike's closing note)

"This is just a quick note about PHP in general: most of the time, developers are going to prefer to use post as opposed to get whenever they're getting information from a form just like this. So a lot of times you'll see people using post more so than using get. Get is going to be used more with URL parameters. But here's the thing — it's really up to you. It's up to you, the developer, to make the decision as far as what you want to be able to happen when that form gets submitted. But I think for the most part, people prefer to use post over get when they're getting information from a form."

## Key metaphor(s)

> "GET is just kind of like anything goes — anyone can see the information, it's up there in the URL."

- GET writes the data on a public noticeboard (the URL); POST sends it through a sealed channel between client and server.

## Gotchas

- The form's `method` **must match** the superglobal you read: `method="get"` → `$_GET`, `method="post"` → `$_POST`. Mixing them (POST form + `$_GET`, or vice versa) gives you an empty value.
- `type="password"` only masks the characters on screen — the value is still fully readable in PHP (and, with GET, in the URL).
- With POST, refreshing a submitted page may re-send the data (browser warning) — a natural side effect of data not being in the URL.
- POST isn't magic: it's more secure *relative to GET*, but the data is still plain text inside the request.

## Modern note

- POST is *not* encryption — HTTPS is what encrypts traffic in transit. POST just keeps data out of the URL.
- A real app never echoes a password back (and never stores it in plain text — hash it). This demo exists purely to show the mechanics.
- Prefer explicit `$_GET` / `$_POST` over `$_REQUEST`, which mixes both (plus cookies) and is less predictable.
- Use `$_SERVER["REQUEST_METHOD"]` (or `$_POST !== []`) to tell whether a POST form was actually submitted before reading it.

## Checkpoint

1. In one sentence each: how does GET send data, how does POST send data?
2. Which superglobal reads a POST form?
3. Switch a form from `method="get"` to `method="post"` and observe what happens to the URL.
4. Which would you use for a login form? Why?
5. Why is a password in the URL "extremely insecure"?

[← Chapter 13 — URL Parameters](13-url-parameters.md) | [Course Map](../README.md) | [Chapter 15 — Arrays →](15-arrays.md)
