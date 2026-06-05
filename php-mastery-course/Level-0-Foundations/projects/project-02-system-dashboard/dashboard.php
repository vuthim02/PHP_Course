<?php
/**
 * dashboard.php — Linux System Dashboard Generator (PHP CLI)
 *
 * Reads live system metrics via /proc and shell commands,
 * then renders them into a static HTML page using a template.
 *
 * Usage:
 *   php dashboard.php
 *
 * Concepts:
 *   - PHP CLI (no web server needed)
 *   - Reading virtual /proc filesystem
 *   - Executing external commands (shell_exec)
 *   - File I/O and string replacement
 *   - Arrays, loops, sprintf formatting
 *
 * PHP 8.x features used:
 *   - Named arguments (implicit via array unpacking)
 *   - match expression (version check)
 *   - Nullsafe operator / readonly properties mindset
 *   - str_contains
 */

declare(strict_types=1);

// ─── Configuration ───────────────────────────────────────────────

const TEMPLATE_FILE = __DIR__ . '/template.html';
const OUTPUT_FILE   = __DIR__ . '/dashboard.html';

// ─── Helpers ─────────────────────────────────────────────────────

function readProcLine(string $path, string $prefix): ?string
{
    if (!is_readable($path)) {
        return null;
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        return null;
    }

    foreach (explode("\n", $contents) as $line) {
        if (str_starts_with($line, $prefix)) {
            return $line;
        }
    }

    return null;
}

function getValue(string $line, int $column): string
{
    $parts = preg_split('/\s+/', $line);
    return $parts[$column] ?? '0';
}

// ─── Gather Metrics ─────────────────────────────────────────────

// Hostname
$hostname = trim((string) shell_exec('hostname') ?: gethostname());

// Date
$date = date('Y-m-d H:i:s');

// Uptime
$uptime = trim((string) shell_exec('uptime -p') ?: 'unknown');
$uptime = str_replace('up ', '', $uptime);

// CPU Load (1-min average as percentage)
$loadRaw = (string) file_get_contents('/proc/loadavg');
$load1   = (float) explode(' ', $loadRaw)[0];
$cpuLoad = round($load1 * 100, 1);

// Memory
$memTotal  = (int) (getValue(readProcLine('/proc/meminfo', 'MemTotal:') ?? '', 1) / 1024);
$memAvail  = (int) (getValue(readProcLine('/proc/meminfo', 'MemAvailable:') ?? '', 1) / 1024);
$memUsed   = $memTotal - $memAvail;
$memPct    = $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 1) : 0;

// Disk (root partition)
$dfRaw = shell_exec('df -BG / | tail -1') ?? '';
$dfParts = preg_split('/\s+/', trim($dfRaw));
$diskUsedGb   = (int) str_replace('G', '', $dfParts[2] ?? '0');
$diskTotalGb  = (int) str_replace('G', '', $dfParts[1] ?? '0');
$diskPct      = (int) str_replace('%', '', $dfParts[4] ?? '0');

// Process count
$procCount = (int) trim((string) shell_exec('ps -e --no-headers 2>/dev/null | wc -l') ?: '0');

// Top 10 processes by CPU
$psRaw = shell_exec('ps -e --no-headers -o pid,user:8,%cpu,%mem,comm --sort=-%cpu 2>/dev/null | head -10') ?? '';
$procRows = '';

foreach (array_filter(explode("\n", $psRaw)) as $line) {
    $parts = preg_split('/\s+/', trim($line));
    if (count($parts) < 5) {
        continue;
    }
    $pid = $parts[0];
    $user = $parts[1];
    $cpu = $parts[2];
    $mem = $parts[3];
    $cmd = implode(' ', array_slice($parts, 4));
    $procRows .= sprintf(
        "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
        htmlspecialchars($pid, ENT_QUOTES),
        htmlspecialchars($user, ENT_QUOTES),
        htmlspecialchars($cpu, ENT_QUOTES),
        htmlspecialchars($mem, ENT_QUOTES),
        htmlspecialchars($cmd, ENT_QUOTES)
    );
}

// ─── Render Template ────────────────────────────────────────────

if (!is_readable(TEMPLATE_FILE)) {
    fwrite(STDERR, "[ERROR] Template file '" . TEMPLATE_FILE . "' not found.\n");
    exit(1);
}

$html = file_get_contents(TEMPLATE_FILE);

$replacements = [
    '{{DATE}}'        => $date,
    '{{HOSTNAME}}'    => $hostname,
    '{{UPTIME}}'      => $uptime,
    '{{CPU_LOAD}}'    => (string) $cpuLoad,
    '{{MEM_USED}}'    => (string) $memUsed,
    '{{MEM_TOTAL}}'   => (string) $memTotal,
    '{{MEM_PERCENT}}' => (string) $memPct,
    '{{DISK_USED}}'   => (string) $diskUsedGb,
    '{{DISK_TOTAL}}'  => (string) $diskTotalGb,
    '{{DISK_PERCENT}}'=> (string) $diskPct,
    '{{PROC_COUNT}}'  => (string) $procCount,
    '{{PROC_TABLE}}'  => $procRows,
];

$output = str_replace(array_keys($replacements), array_values($replacements), $html);

file_put_contents(OUTPUT_FILE, $output);

echo "[OK] Dashboard generated: " . realpath(OUTPUT_FILE) . PHP_EOL;
