# Project 4: File Organizer CLI Tool

A PHP CLI script that scans a directory and organizes files into category folders based on their **file extension** (`.jpg` → `Images/`, `.pdf` → `Documents/`, `.zip` → `Archives/`, `.php` → `Code/`, etc.). Features a dry-run mode, collision detection, and an editable configuration file.

## Learning Objectives

- Write and run a **PHP CLI application** (no web server)
- Understand **filesystem operations**: reading directories, creating folders, moving files
- Work with **file extensions** and MIME-type-like categorization
- Parse **command-line arguments** (`$argv`) and implement flags (`--dry-run`)
- Design a **configuration-driven** architecture (extensions defined in `config.php`)
- Handle **edge cases**: files with no extension, name collisions, symlinks, permission errors
- Practice **error handling** with exceptions and try/catch

## Features

| Feature | Description |
|---------|-------------|
| **Extension Mapping** | 70+ extensions mapped to 8 categories (Images, Documents, Archives, Code, Audio, Video, Other) |
| **Dry-Run Mode** | `--dry-run` flag shows what would happen without moving anything |
| **Collision Detection** | Skips files if a same-named file already exists in the destination |
| **Subfolder Grouping** | Optional A–Z subfolders (configurable in `config.php`) |
| **Logging** | Optional log file of all operations |
| **Help Flag** | `--help` or `-h` shows usage |
| **No Dependencies** | Pure PHP 8.x — no Composer packages required |

## How to Run

```bash
cd project-04-file-organizer

# Organize the current directory
php organize.php

# Organize a specific directory
php organize.php ~/Downloads

# Preview changes without moving anything
php organize.php --dry-run ~/Downloads

# Show help
php organize.php --help
```

### Quick Test

Create some test files and run the organizer:

```bash
mkdir -p test-files && cd test-files
touch photo.jpg document.pdf archive.zip script.php audio.mp3 video.mp4 notes.txt
php ../organize.php .
# Files are moved into Images/, Documents/, Archives/, Code/, Audio/, Video/
```

## Expected Output

**Dry-run mode:**
```
Scanning: /home/user/Downloads
──────────────────────────────────────────────────
  [DRY] photo.jpg                               → Images/
  [DRY] document.pdf                            → Documents/
  [DRY] archive.zip                             → Archives/
  [DRY] script.php                              → Code/
  [DRY] audio.mp3                               → Audio/
  [DRY] video.mp4                               → Video/
  [DRY] notes.txt                               → Documents/
──────────────────────────────────────────────────
  DRY RUN — No files were moved
  Files moved:   7
  Files skipped: 0
```

**Actual run:** Same output, but `[DRY]` becomes `[ OK ]` and files are physically moved.

## Code Structure

```
project-04-file-organizer/
├── organize.php       # Main CLI script — scans, categorizes, moves
├── config.php         # Extension-to-folder mapping + settings
└── README.md
```

### File Responsibilities

- **`config.php`** — Defines the `EXTENSION_MAP` constant (extension → folder), `DRY_RUN` default, `SUBFOLDER_BY_LETTER` toggle, `LOG_FILE` path, and `IGNORED_DIRECTORIES`. Edit this file to customize categories.
- **`organize.php`** — Contains the `FileOrganizer` class with:
  - Constructor: validates the target directory, resolves to real path
  - `run()`: iterates files, calls `processItem()` for each
  - `processItem()`: checks extension, looks up category, handles collisions, moves file
  - `printSummary()`: prints final stats
  - CLI entry point: parses `$argv` for directory and `--dry-run` / `--help` flags

### Customization

To add a new category or extension, edit `EXTENSION_MAP` in `config.php`:

```php
const EXTENSION_MAP = [
    'exe'  => 'Executables',   // new category
    'dmg'  => 'Executables',
    'deb'  => 'Packages',      // another new one
    'rpm'  => 'Packages',
    // ... existing mappings
];
```

## Concepts Practiced

| Concept | How It's Used |
|---------|---------------|
| **Operating Systems** | File systems, directory hierarchy, file permissions, rename operations |
| **Linux CLI** | Running PHP scripts, navigating directories, using `--flags` |
| **PHP CLI** | `$argv`, `STDOUT`/`STDERR`, exit codes, `php -f` execution |
| **Filesystem API** | `scandir`, `FilesystemIterator`, `mkdir`, `rename`, `file_exists`, `realpath` |
| **Strings** | Extension extraction (`pathinfo`, `strtolower`), string formatting (`sprintf`) |
| **Arrays** | Key/value mapping (extension → folder), array iteration |
| **Error handling** | `Try/catch`, `Throwable`, `InvalidArgumentException` |
| **Configuration** | Decoupling settings from logic via a separate config file |
