#!/usr/bin/env php
<?php
/**
 * organize.php — File Organizer CLI Tool
 *
 * Scans a directory for files and moves them into category folders
 * based on their extension (Images/, Documents/, Archives/, Code/, etc.).
 *
 * Usage:
 *   php organize.php                     # Organize current directory
 *   php organize.php /path/to/folder     # Organize specified directory
 *   php organize.php --dry-run /path     # Preview without moving
 *
 * PHP 8.x features:
 *   - Named arguments, match expression
 *   - str_starts_with, str_contains
 *   - readonly properties promoted
 *   - Constructor property promotion (in FileOrganizer)
 *   - fdiv for safe division
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

// ─── Main Organizer Class ───────────────────────────────────────

final class FileOrganizer
{
    private string $targetDir;
    private int $moved = 0;
    private int $skipped = 0;
    private array $errors = [];
    private bool $dryRun;

    public function __construct(string $targetDir, bool $dryRun = false)
    {
        $real = realpath($targetDir);

        if ($real === false || !is_dir($real)) {
            throw new InvalidArgumentException(
                "Directory does not exist: {$targetDir}"
            );
        }

        $this->targetDir = $real;
        $this->dryRun = $dryRun;
    }

    public function run(): void
    {
        $this->writeln("Scanning: {$this->targetDir}");
        $this->writeln(str_repeat('─', 50));

        $items = new FilesystemIterator(
            $this->targetDir,
            FilesystemIterator::SKIP_DOTS
        );

        foreach ($items as $item) {
            $this->processItem($item);
        }

        $this->printSummary();
        $this->writeLog();
    }

    private function processItem(SplFileInfo $item): void
    {
        if ($item->isDir()) {
            return; // Don't move directories
        }

        if (!$item->isFile() || $item->isLink()) {
            $this->skipped++;
            return;
        }

        $extension = strtolower($item->getExtension());

        if ($extension === '') {
            $this->skipped++;
            return;
        }

        // Look up category from config
        $folder = EXTENSION_MAP[$extension] ?? 'Other';

        // Build destination path
        $destDir = $this->targetDir . DIRECTORY_SEPARATOR . $folder;

        if (SUBFOLDER_BY_LETTER) {
            $firstLetter = strtoupper($item->getBasename())[0];
            if (ctype_alpha($firstLetter)) {
                $destDir .= DIRECTORY_SEPARATOR . $firstLetter;
            } else {
                $destDir .= DIRECTORY_SEPARATOR . '#';
            }
        }

        $destPath = $destDir . DIRECTORY_SEPARATOR . $item->getFilename();

        // Skip if destination is the same as source
        if ($item->getPathname() === $destPath) {
            $this->skipped++;
            return;
        }

        // Check for name collision
        if (file_exists($destPath)) {
            $this->errors[] = "Collision: {$item->getFilename()} already exists in {$folder}/";
            $this->skipped++;
            return;
        }

        // Execute or simulate
        if ($this->dryRun) {
            $this->writeln(
                sprintf("  [DRY] %-40s → %s/", $item->getFilename(), $folder)
            );
            $this->moved++;
        } else {
            if (!is_dir($destDir)) {
                if (!mkdir($destDir, 0755, true)) {
                    $this->errors[] = "Failed to create directory: {$destDir}";
                    $this->skipped++;
                    return;
                }
            }

            if (rename($item->getPathname(), $destPath)) {
                $this->writeln(
                    sprintf("  [ OK] %-40s → %s/", $item->getFilename(), $folder)
                );
                $this->moved++;
            } else {
                $this->errors[] = "Failed to move: {$item->getFilename()}";
                $this->skipped++;
            }
        }
    }

    private function printSummary(): void
    {
        $this->writeln(str_repeat('─', 50));
        $mode = $this->dryRun ? 'DRY RUN — No files were moved' : 'Done';
        $this->writeln("  {$mode}");
        $this->writeln("  Files moved:   {$this->moved}");
        $this->writeln("  Files skipped: {$this->skipped}");

        if ($this->errors !== []) {
            $this->writeln("  Errors:");
            foreach ($this->errors as $err) {
                $this->writeln("    • {$err}");
            }
        }
    }

    private function writeLog(): void
    {
        if (LOG_FILE === null) {
            return;
        }

        $entry = sprintf(
            "[%s] Moved: %d | Skipped: %d | Errors: %d | Dir: %s\n",
            date('Y-m-d H:i:s'),
            $this->moved,
            $this->skipped,
            count($this->errors),
            $this->targetDir
        );

        file_put_contents(LOG_FILE, $entry, FILE_APPEND | LOCK_EX);
    }

    private function writeln(string $line): void
    {
        fwrite(STDOUT, $line . PHP_EOL);
    }
}

// ─── CLI Entry Point ────────────────────────────────────────────

function showHelp(): never
{
    echo <<<HELP
File Organizer — Organize files by extension into category folders.

Usage:
  php organize.php [options] [directory]

Options:
  --dry-run    Preview changes without moving any files
  --help       Show this help message

Arguments:
  directory    Path to the directory to organize (default: current directory)

Examples:
  php organize.php
  php organize.php ~/Downloads
  php organize.php --dry-run ~/Downloads

HELP;
    exit(0);
}

// Parse CLI arguments
$args = array_slice($argv ?? [], 1);
$targetDir = getcwd();
$dryRun = DRY_RUN;

foreach ($args as $arg) {
    if ($arg === '--help' || $arg === '-h') {
        showHelp();
    } elseif ($arg === '--dry-run') {
        $dryRun = true;
    } elseif (str_starts_with($arg, '-')) {
        fwrite(STDERR, "Unknown option: {$arg}\n");
        exit(1);
    } else {
        $targetDir = $arg;
    }
}

// Run
try {
    $organizer = new FileOrganizer($targetDir, $dryRun);
    $organizer->run();
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] " . $e->getMessage() . PHP_EOL);
    exit(1);
}
