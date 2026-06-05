<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

if (PHP_SAPI !== 'cli') {
    die('This script must be run from the command line.');
}

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'add':
        $description = $argv[2] ?? '';
        if ($description === '') {
            echo "Error: Please provide a task description.\n";
            echo "Usage: php todo.php add \"Buy groceries\"\n";
            exit(1);
        }
        addTask($description);
        break;

    case 'list':
        $filter = $argv[2] ?? null;
        if ($filter !== null && !in_array($filter, ['done', 'pending'], true)) {
            echo "Error: Invalid filter \"{$filter}\". Use 'done' or 'pending'.\n";
            exit(1);
        }
        listTasks($filter);
        break;

    case 'done':
        $id = (int) ($argv[2] ?? 0);
        if ($id <= 0) {
            echo "Error: Please provide a valid task ID.\n";
            echo "Usage: php todo.php done 3\n";
            exit(1);
        }
        markDone($id);
        break;

    case 'delete':
        $id = (int) ($argv[2] ?? 0);
        if ($id <= 0) {
            echo "Error: Please provide a valid task ID.\n";
            echo "Usage: php todo.php delete 3\n";
            exit(1);
        }
        deleteTask($id);
        break;

    case 'search':
        $term = $argv[2] ?? '';
        if ($term === '') {
            echo "Error: Please provide a search term.\n";
            echo "Usage: php todo.php search \"groceries\"\n";
            exit(1);
        }
        searchTasks($term);
        break;

    case 'help':
    default:
        printUsage();
        break;
}
