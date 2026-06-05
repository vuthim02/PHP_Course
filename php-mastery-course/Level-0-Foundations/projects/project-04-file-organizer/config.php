<?php
/**
 * config.php — File Organizer Configuration
 *
 * Maps file extensions to destination folder names.
 * Edit this file to customize how your files are organized.
 *
 * PHP 8.x: Using readonly class for type-safe configuration.
 */

declare(strict_types=1);

/**
 * Extension-to-folder mapping.
 * Keys are lowercase file extensions (without dot).
 * Values are destination folder names (created relative to target directory).
 */
const EXTENSION_MAP = [
    // ─── Images ────────────────────────────────────
    'jpg'  => 'Images',
    'jpeg' => 'Images',
    'png'  => 'Images',
    'gif'  => 'Images',
    'svg'  => 'Images',
    'webp' => 'Images',
    'bmp'  => 'Images',
    'ico'  => 'Images',

    // ─── Documents ─────────────────────────────────
    'pdf'  => 'Documents',
    'doc'  => 'Documents',
    'docx' => 'Documents',
    'xls'  => 'Documents',
    'xlsx' => 'Documents',
    'ppt'  => 'Documents',
    'pptx' => 'Documents',
    'odt'  => 'Documents',
    'ods'  => 'Documents',
    'csv'  => 'Documents',
    'txt'  => 'Documents',
    'md'   => 'Documents',
    'rtf'  => 'Documents',

    // ─── Archives ──────────────────────────────────
    'zip'  => 'Archives',
    'tar'  => 'Archives',
    'gz'   => 'Archives',
    'bz2'  => 'Archives',
    'xz'   => 'Archives',
    'rar'  => 'Archives',
    '7z'   => 'Archives',

    // ─── Code & Scripts ────────────────────────────
    'php'  => 'Code',
    'html' => 'Code',
    'css'  => 'Code',
    'js'   => 'Code',
    'ts'   => 'Code',
    'json' => 'Code',
    'xml'  => 'Code',
    'yaml' => 'Code',
    'yml'  => 'Code',
    'sql'  => 'Code',
    'sh'   => 'Code',
    'py'   => 'Code',
    'rb'   => 'Code',
    'go'   => 'Code',
    'rs'   => 'Code',
    'c'    => 'Code',
    'cpp'  => 'Code',
    'h'    => 'Code',
    'java' => 'Code',

    // ─── Audio ─────────────────────────────────────
    'mp3'  => 'Audio',
    'wav'  => 'Audio',
    'flac' => 'Audio',
    'aac'  => 'Audio',
    'ogg'  => 'Audio',
    'wma'  => 'Audio',

    // ─── Video ─────────────────────────────────────
    'mp4'  => 'Video',
    'avi'  => 'Video',
    'mkv'  => 'Video',
    'mov'  => 'Video',
    'webm' => 'Video',
    'flv'  => 'Video',
];

/**
 * Directories to ignore when organizing.
 * These will never be moved or deleted.
 */
const IGNORED_DIRECTORIES = [
    '.',
    '..',
];

/**
 * Whether to simulate the operation (dry-run). When true,
 * no files are actually moved — only a report is printed.
 */
const DRY_RUN = false;

/**
 * Whether to move files into sub-folders by the first letter
 * of the filename (e.g., Images/A/, Images/B/). Set to false
 * to put all files directly into the category folder.
 */
const SUBFOLDER_BY_LETTER = false;

/**
 * Log file path. Set to null to disable logging.
 */
const LOG_FILE = null; // e.g., __DIR__ . '/organizer.log';
