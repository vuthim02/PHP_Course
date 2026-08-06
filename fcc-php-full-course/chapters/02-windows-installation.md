# Chapter 02 — Windows Installation

**Video section 2 (1:56)**

## What the video teaches

Mike installs PHP on Windows, step by step, in the order you should follow. He starts by setting your expectations: **PHP is actually pretty easy to set up** — "we basically just have to download a few files, and we're actually gonna have to modify something in our Windows computer, and then we should be able to have everything set up and ready to go." There are exactly four steps: download, extract, configure PATH, verify.

### Step 1 — Download PHP

1. Open your web browser and go to **php.net** (a "pretty simple web address").
2. On the homepage there's a **Downloads** option — click it.
3. On the downloads page you'll see a bunch of different options. Because you're on Windows, click **Windows Downloads**.
4. You'll now see the **latest version of PHP** available (in the video, that's **7.1**), with a bunch of different build options. Pick the version that's appropriate for your operating system:
   - Mike has a **64-bit** operating system, so he chooses the **64-bit** build.
   - There are **Thread Safe (TS)** and **Non-Thread Safe** builds. Mike chooses the **thread safe** version (a good pick if you plan to use PHP with a web server that uses threads).
   - If you have a **32-bit** operating system, there's a separate option up at the top for you.
5. Click the **zip file** for your chosen build. It downloads a `.zip` that **has all the files we need to start using PHP**.

### Step 2 — Extract the files

1. Hop over to your **Downloads folder** — you'll see the zip file sitting there.
2. **Extract all** the files in the zip into another folder.
3. In the extract dialog, type the name of the folder where you want to store the files. For simplicity Mike stores them at the **root directory of the C drive** inside a folder called **PHP** — so the final location is `C:\PHP`. (You can put it somewhere else if you like, but to fully follow along, put it at the root of your C drive.)
4. Click **Extract**. When it finishes, the PHP folder opens: "here I am on the C drive, and over here we have our PHP folder, and in here there's just a bunch of files."
5. **You don't have to worry about any of these files** — and, importantly, don't touch any of them or modify any of them. "As long as we have these here in this folder, then we're ready to go."

### Step 3 — Configure the Windows PATH variable

Now you have everything downloaded, but there's one more thing to do: **configure the PATH variable**. Essentially, you have to **tell Windows that PHP is inside of this folder** — you're going to want to run PHP and use all of its functionality, and Windows needs to know where to find it.

1. Go to the **search bar** on the taskbar and start typing **"environment"** (e-n-v-i-r-o-n-m-e-n-t).
2. The option **"Edit the system environment variables"** should pop up — click it.
3. A window opens; down at the bottom there's an **Environment Variables** button — click it.
4. In the new window, find the variable called **Path** (you'll see it under the system/user variables lists) and click **Edit**.
5. The PATH variable is "just a Windows system variable that kind of tells Windows where a bunch of executable files are." You're going to add PHP to that list.
6. Click **New**, and in the little typing box that appears, type the location of the folder where you extracted PHP: **`C:\PHP`** (capital C, colon, backslash, PHP).
7. Click **OK** — the entry gets added to the PATH variable.
8. Click **OK** out of the remaining dialogs to close them all.

**Verify the PATH update:** Open the search bar again, type **CMD**, and open **Command Prompt** (a program that lets you interact with the computer using text commands). Type:

```bash
echo %PATH%
```

This prints out the entire PATH variable (type it in all capitals, surrounded by `%` signs). **Confirm that the `C:\PHP` entry shows up in the output.** As long as it's there, you've successfully updated the PATH variable.

### Step 4 — Verify PHP works

In the same Command Prompt window, type:

```bash
php -v
```

and press Enter. You should see a **version number** pop up for PHP.

> "As long as you get a version number popping up here and you're not getting any errors, then you've officially installed PHP on your computer."

And that's genuinely everything you need to start writing PHP and building an awesome website. Mike closes by pointing you to the next tutorial, where you'll create your first PHP file.

## Key metaphor(s)

> The PATH variable is "a Windows system variable that kind of tells Windows where a bunch of executable files are."

So adding `C:\PHP` to the PATH is like adding PHP's folder to Windows' mental address book of "places I can find programs."

## Gotchas

- **Pick the right build:** 64-bit vs 32-bit must match your OS; choose **Thread Safe** if you plan to use PHP with a threaded web server (that's what Mike picks).
- **Don't modify or delete the files** inside the PHP folder — everything just needs to stay there.
- The PATH change only affects **newly opened** Command Prompt windows. If `php -v` doesn't work, close CMD and open a fresh one so it picks up the updated PATH.
- `echo %PATH%` must be typed with the `%` signs and in capitals (`%PATH%`) to expand to the variable's value.
- The version number in the video is **7.1** — that's just the current release at the time of filming. Install the current stable version; the process is identical.

## Modern note

The steps are the same today (php.net → Downloads → Windows Downloads → pick the **x64 Thread Safe** zip → extract → add the folder to PATH → `php -v`), though the version is now well past 7.1 and the build naming is slightly different. Many beginners instead install PHP bundled with **XAMPP**, **WAMP**, or **Laragon**, which do all of this for you.

On **Linux** (like this machine) you don't manage PATH manually at all — the package manager does it:

```bash
sudo dnf install php          # Fedora / RHEL
sudo apt install php          # Debian / Ubuntu
```

On our machine this is already done — `php -v` reports **PHP 8.5.9 (cli)**. Later in the course you'll also want extension packages like `php-mysql` and `php-xml` for web/DB work.

## Checkpoint

1. What does the PATH variable do, in your own words?
2. What single command proves PHP is installed, and what should it output?
3. Why should you not modify or delete the files inside the PHP folder?
4. Why is Thread Safe the build Mike chooses?
5. Run `php -v` on this machine and read the version line out loud. (On Linux you can also run `which php` to see where it's installed.)

[← Chapter 01 — Introduction](01-introduction.md) | [Course Map](../README.md) | [Chapter 03 — Choosing a Text Editor →](03-choosing-a-text-editor.md)
