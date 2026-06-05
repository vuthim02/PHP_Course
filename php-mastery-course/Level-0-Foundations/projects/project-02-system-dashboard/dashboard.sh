#!/usr/bin/env bash
# -------------------------------------------------------------------
# dashboard.sh — Linux System Dashboard Generator (Bash)
# Reads live system metrics and renders them into a static HTML page.
#
# Usage:
#   chmod +x dashboard.sh && ./dashboard.sh
#
# Concepts: Linux commands, pipes, process management, file I/O,
#           string interpolation, templating.
# -------------------------------------------------------------------
set -euo pipefail

OUTPUT_FILE="dashboard.html"
TEMPLATE_FILE="template.html"

# ---- Helper: fail early if template is missing ----
if [ ! -f "$TEMPLATE_FILE" ]; then
    echo "[ERROR] Template file '$TEMPLATE_FILE' not found."
    echo "        Make sure you run this script from the project directory."
    exit 1
fi

# ---- Gather system data ----
HOSTNAME="$(hostname)"
DATE="$(date '+%Y-%m-%d %H:%M:%S')"
UPTIME="$(uptime -p | sed 's/^up //')"

# CPU: average load over 1 min as percentage of 100
CPU_LOAD="$(awk '{printf "%.1f", $1 * 100}' /proc/loadavg)"

# Memory (MB)
MEM_TOTAL="$(awk '/^MemTotal:/{printf "%.0f", $2/1024}' /proc/meminfo)"
MEM_AVAIL="$(awk '/^MemAvailable:/{printf "%.0f", $2/1024}' /proc/meminfo)"
MEM_USED=$(( MEM_TOTAL - MEM_AVAIL ))
MEM_PERCENT="$(awk "BEGIN {printf \"%.1f\", ($MEM_USED / $MEM_TOTAL) * 100}")"

# Disk (root partition, GB)
DISK_INFO="$(df -BG / | awk 'NR==2 {used=substr($3,1,length($3)-1); total=substr($2,1,length($2)-1); percent=$5; print used, total, percent}')"
DISK_USED="$(echo "$DISK_INFO" | cut -d' ' -f1)"
DISK_TOTAL="$(echo "$DISK_INFO" | cut -d' ' -f2)"
DISK_PERCENT="$(echo "$DISK_INFO" | cut -d' ' -f3 | tr -d '%')"

# Process count
PROC_COUNT="$(ps -e --no-headers 2>/dev/null | wc -l || echo "0")"

# Top 10 processes by CPU
PROC_TABLE=""
while IFS=' ' read -r pid user cpu mem cmd; do
    PROC_TABLE+="<tr><td>${pid}</td><td>${user}</td><td>${cpu}</td><td>${mem}</td><td>${cmd}</td></tr>"$'\n'
done < <(ps -e --no-headers -o pid,user:8,%cpu,%mem,comm --sort=-%cpu 2>/dev/null | head -10)

# ---- Render ----
HTML="$(cat "$TEMPLATE_FILE")"

HTML="${HTML//{{DATE}}/$DATE}"
HTML="${HTML//{{HOSTNAME}}/$HOSTNAME}"
HTML="${HTML//{{UPTIME}}/$UPTIME}"
HTML="${HTML//{{CPU_LOAD}}/$CPU_LOAD}"
HTML="${HTML//{{MEM_USED}}/$MEM_USED}"
HTML="${HTML//{{MEM_TOTAL}}/$MEM_TOTAL}"
HTML="${HTML//{{MEM_PERCENT}}/$MEM_PERCENT}"
HTML="${HTML//{{DISK_USED}}/$DISK_USED}"
HTML="${HTML//{{DISK_TOTAL}}/$DISK_TOTAL}"
HTML="${HTML//{{DISK_PERCENT}}/$DISK_PERCENT}"
HTML="${HTML//{{PROC_COUNT}}/$PROC_COUNT}"
HTML="${HTML//{{PROC_TABLE}}/$PROC_TABLE}"

echo "$HTML" > "$OUTPUT_FILE"
echo "[OK] Dashboard generated: $OUTPUT_FILE"
echo "     Open in browser: file://$(pwd)/$OUTPUT_FILE"
