# Project 2: Linux System Dashboard

A server-side dashboard generator that reads **live Linux system metrics** (CPU, memory, disk, processes) and renders them into a beautiful static HTML page. Provided in both **Bash** and **PHP CLI** so you can compare approaches.

## Learning Objectives

- Navigate the **Linux filesystem** (`/proc`, `/sys`) to read kernel metrics
- Use **command-line tools** (`hostname`, `uptime`, `ps`, `df`, `awk`, `sed`)
- Understand **process management** (`ps` flags, process states, `/proc/loadavg`)
- Practice **shell scripting** (variables, pipes, string substitution, I/O redirection)
- Practice **PHP CLI** scripting (no web server required)
- Learn the **template rendering** pattern (placeholder → replacement)
- Distinguish between **CLI programs** and **web applications**

## Features

| Feature | Description |
|---------|-------------|
| **CPU Load** | 1-minute average load as percentage |
| **Memory Usage** | Total, used, available (MB), and percentage |
| **Disk Usage** | Root partition total, used (GB), and percentage |
| **Uptime** | Human-readable system uptime |
| **Process Count** | Total running processes on the system |
| **Top Processes** | Top 10 processes sorted by CPU usage (PID, user, CPU%, MEM%, command) |
| **Dark Theme** | Modern dark UI (Tailwind-inspired color palette) |
| **Dual Implementation** | Bash script and PHP CLI script — compare them side by side |

## How to Run

### Prerequisites

- **Linux** (reads from `/proc`; not available on macOS/Windows without WSL)
- **PHP 8.0+** (only needed for the PHP version)
- `hostname`, `uptime`, `ps`, `df`, `awk` standard utilities (present on every Linux distro)

### Bash Version

```bash
cd project-02-system-dashboard
chmod +x dashboard.sh
./dashboard.sh
```

Open the generated `dashboard.html` in your browser.

### PHP Version

```bash
cd project-02-system-dashboard
php dashboard.php
```

Open the generated `dashboard.html` in your browser.

## Expected Output

A single `dashboard.html` file that opens in any browser showing:

- **Header**: hostname, generation timestamp, uptime
- **Metric Cards** (4-column responsive grid):
  - CPU Load (e.g., `24.3%`)
  - Memory (e.g., `2048 / 7852 MB` with `26.1% used`)
  - Disk (e.g., `32 / 120 GB` with `26.7% used`)
  - Processes (e.g., `312`)
- **Process Table**: 10 rows showing PID, User, CPU%, MEM%, Command
- **Footer**: attribution

The page is fully self-contained (no external dependencies, inline CSS).

## Code Structure

```
project-02-system-dashboard/
├── dashboard.sh       # Bash script — gathers metrics and renders template
├── dashboard.php      # PHP CLI script — same logic in PHP
├── template.html      # HTML template with {{PLACEHOLDER}} tokens
└── README.md
```

### How the Template Works

`template.html` contains `{{PLACEHOLDER}}` tokens. Both scripts:

1. Read `template.html` into a string
2. Gather system metrics
3. Replace each `{{PLACEHOLDER}}` with the corresponding value
4. Write the result to `dashboard.html`

This is the same pattern used by PHP templating engines (Blade, Twig, Smarty) and static site generators.

## Concepts Practiced

| Concept | How It's Used |
|---------|---------------|
| **How computers work** | `/proc` is a virtual filesystem — kernel data exposed as files |
| **Operating Systems** | Process list, memory management, filesystem hierarchy |
| **Linux CLI** | `hostname`, `uptime`, `ps`, `df`, `awk`, pipes, redirects |
| **Bash scripting** | Variables, arrays, `while read`, string substitution `${var//pattern/replacement}` |
| **PHP CLI** | `file_get_contents`, `shell_exec`, `str_replace`, `htmlspecialchars` |
| **File I/O** | Reading templates, writing output files |
| **HTML/CSS** | Semantic structure, dark theme, responsive grid layout |
| **Templating pattern** | Placeholder substitution — a foundational web dev concept |
