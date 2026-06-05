<?php

declare(strict_types=1);

define('DATA_FILE', __DIR__ . '/expenses.json');
define('CATEGORIES', ['Food', 'Transport', 'Housing', 'Entertainment', 'Utilities', 'Health', 'Shopping', 'Education', 'Other']);

function loadExpenses(): array
{
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(DATA_FILE);
    return is_array($decoded = json_decode($data, true)) ? $decoded : [];
}

function saveExpenses(array $expenses): void
{
    $dir = dirname(DATA_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(
        DATA_FILE,
        json_encode($expenses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function addExpense(float $amount, string $category, string $description): void
{
    if ($amount <= 0) {
        echo "Error: Amount must be positive.\n";
        exit(1);
    }
    if (!in_array($category, CATEGORIES, true)) {
        echo "Error: Invalid category. Valid: " . implode(', ', CATEGORIES) . "\n";
        exit(1);
    }
    if (trim($description) === '') {
        echo "Error: Description cannot be empty.\n";
        exit(1);
    }

    $expenses = loadExpenses();

    $id = count($expenses) > 0 ? max(array_column($expenses, 'id')) + 1 : 1;

    $expenses[] = [
        'id'          => $id,
        'amount'      => round($amount, 2),
        'category'    => $category,
        'description' => $description,
        'date'        => date('Y-m-d'),
        'created_at'  => date('Y-m-d H:i:s'),
    ];

    saveExpenses($expenses);
    printf("Expense added: $%.2f [%s] %s (ID: %d)\n", $amount, $category, $description, $id);
}

function listExpenses(): void
{
    $expenses = loadExpenses();

    if (empty($expenses)) {
        echo "No expenses recorded.\n";
        return;
    }

    $total = 0;
    echo str_repeat('-', 72) . "\n";
    echo sprintf("%-4s %-10s %-14s %-30s %-10s\n", 'ID', 'Date', 'Category', 'Description', 'Amount');
    echo str_repeat('-', 72) . "\n";

    foreach ($expenses as $e) {
        $desc = strlen($e['description']) > 28
            ? substr($e['description'], 0, 25) . '...'
            : $e['description'];
        printf(
            "%-4d %-10s %-14s %-30s $%-8.2f\n",
            $e['id'],
            $e['date'],
            $e['category'],
            $desc,
            $e['amount']
        );
        $total += $e['amount'];
    }
    echo str_repeat('-', 72) . "\n";
    printf("%60s $%-8.2f\n", 'Total:', $total);
}

function showCategoryTotals(): void
{
    $expenses = loadExpenses();

    if (empty($expenses)) {
        echo "No expenses recorded.\n";
        return;
    }

    $totals = [];
    foreach ($expenses as $e) {
        $cat = $e['category'];
        $totals[$cat] = ($totals[$cat] ?? 0) + $e['amount'];
    }

    // Sort by total descending
    arsort($totals);

    $grandTotal = array_sum($totals);

    echo str_repeat('-', 48) . "\n";
    echo sprintf("%-20s %-14s %s\n", 'Category', 'Total', '%');
    echo str_repeat('-', 48) . "\n";

    foreach ($totals as $cat => $total) {
        $pct = $grandTotal > 0 ? round(($total / $grandTotal) * 100, 1) : 0;
        printf("%-20s $%-11.2f %4.1f%%\n", $cat, $total, $pct);
    }
    echo str_repeat('-', 48) . "\n";
    printf("%-20s $%-11.2f\n", 'Grand Total', $grandTotal);
}

function showMonthlySummary(?string $month = null): void
{
    $expenses = loadExpenses();

    if (empty($expenses)) {
        echo "No expenses recorded.\n";
        return;
    }

    // Group by year-month
    $byMonth = [];
    foreach ($expenses as $e) {
        $ym = substr($e['date'], 0, 7); // YYYY-MM
        $byMonth[$ym][] = $e;
    }

    // Sort months descending
    krsort($byMonth);

    if ($month !== null) {
        // Validate format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            echo "Error: Month must be in YYYY-MM format (e.g. 2025-03).\n";
            exit(1);
        }
        $byMonth = array_intersect_key($byMonth, [$month => true]);
        if (empty($byMonth)) {
            echo "No expenses for {$month}.\n";
            return;
        }
    }

    foreach ($byMonth as $ym => $items) {
        $monthTotal = 0;
        $catCounts = [];
        foreach ($items as $e) {
            $monthTotal += $e['amount'];
            $cat = $e['category'];
            $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
        }

        echo "\n--- {$ym} ---\n";
        printf("  Total: $%.2f (%d entries)\n", $monthTotal, count($items));
        echo "  Categories:\n";
        foreach ($catCounts as $cat => $count) {
            echo "    {$cat}: {$count}\n";
        }
    }
}

function exportCsv(): void
{
    $expenses = loadExpenses();

    $filename = 'expenses_' . date('Y-m-d') . '.csv';

    $handle = fopen($filename, 'w');
    if ($handle === false) {
        echo "Error: Could not create CSV file.\n";
        exit(1);
    }

    // Header
    fputcsv($handle, ['ID', 'Date', 'Category', 'Description', 'Amount', 'Created At']);

    foreach ($expenses as $e) {
        fputcsv($handle, [
            $e['id'],
            $e['date'],
            $e['category'],
            $e['description'],
            number_format($e['amount'], 2),
            $e['created_at'],
        ]);
    }

    fclose($handle);
    echo "Exported to {$filename} (" . count($expenses) . " entries)\n";
}

function deleteExpense(int $id): void
{
    $expenses = loadExpenses();
    $before = count($expenses);

    $expenses = array_values(
        array_filter($expenses, fn($e) => $e['id'] !== $id)
    );

    if (count($expenses) === $before) {
        echo "Error: Expense #{$id} not found.\n";
        exit(1);
    }

    saveExpenses($expenses);
    echo "Expense #{$id} deleted.\n";
}

function printUsage(): void
{
    echo "Usage: php expense.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  add <amount> <category> <description>   Add an expense\n";
    echo "  list                                     List all expenses\n";
    echo "  totals                                   Show totals by category\n";
    echo "  monthly [YYYY-MM]                        Show monthly summary\n";
    echo "  export                                   Export all to CSV\n";
    echo "  delete <id>                              Delete an expense\n";
    echo "  help                                     Show this help\n\n";
    echo "Categories: " . implode(', ', CATEGORIES) . "\n";
}
