<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

if (PHP_SAPI !== 'cli') {
    die('This script must be run from the command line.');
}

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'add':
        if (count($argv) < 5) {
            echo "Usage: php expense.php add <amount> <category> <description>\n";
            echo "Example: php expense.php add 12.50 Food \"Lunch at subway\"\n";
            exit(1);
        }
        $amount = (float) ($argv[2] ?? 0);
        $category = $argv[3] ?? '';
        $description = $argv[4] ?? '';
        addExpense($amount, $category, $description);
        break;

    case 'list':
        listExpenses();
        break;

    case 'totals':
        showCategoryTotals();
        break;

    case 'monthly':
        $month = $argv[2] ?? null;
        showMonthlySummary($month);
        break;

    case 'export':
        exportCsv();
        break;

    case 'delete':
        $id = (int) ($argv[2] ?? 0);
        if ($id <= 0) {
            echo "Usage: php expense.php delete <id>\n";
            exit(1);
        }
        deleteExpense($id);
        break;

    case 'help':
    default:
        printUsage();
        break;
}
