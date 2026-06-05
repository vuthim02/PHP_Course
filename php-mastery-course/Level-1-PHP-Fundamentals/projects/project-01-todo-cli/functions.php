<?php

declare(strict_types=1);

define('TASKS_FILE', __DIR__ . '/tasks.json');

function loadTasks(): array
{
    if (!file_exists(TASKS_FILE)) {
        return [];
    }
    $content = file_get_contents(TASKS_FILE);
    if ($content === false) {
        return [];
    }
    $tasks = json_decode($content, true);
    return is_array($tasks) ? $tasks : [];
}

function saveTasks(array $tasks): void
{
    $dir = dirname(TASKS_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents(
        TASKS_FILE,
        json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function addTask(string $description): void
{
    if (trim($description) === '') {
        echo "Error: Task description cannot be empty.\n";
        exit(1);
    }

    $tasks = loadTasks();

    $id = count($tasks) > 0 ? max(array_column($tasks, 'id')) + 1 : 1;

    $tasks[] = [
        'id'          => $id,
        'description' => $description,
        'done'        => false,
        'created_at'  => date('Y-m-d H:i:s'),
    ];

    saveTasks($tasks);
    echo "Task added successfully [ID: {$id}].\n";
}

function listTasks(?string $filter = null): void
{
    $tasks = loadTasks();

    if (empty($tasks)) {
        echo "No tasks found.\n";
        return;
    }

    if ($filter === 'done') {
        $tasks = array_filter($tasks, fn($t) => $t['done']);
    } elseif ($filter === 'pending') {
        $tasks = array_filter($tasks, fn($t) => !$t['done']);
    }

    if (empty($tasks)) {
        echo "No tasks match the filter.\n";
        return;
    }

    echo str_repeat('-', 60) . "\n";
    echo sprintf("%-4s %-8s %-35s %-19s\n", 'ID', 'Status', 'Description', 'Created');
    echo str_repeat('-', 60) . "\n";

    foreach ($tasks as $task) {
        $status = $task['done'] ? '[Done]' : '[  ]';
        $desc = strlen($task['description']) > 33
            ? substr($task['description'], 0, 30) . '...'
            : $task['description'];
        echo sprintf(
            "%-4d %-8s %-35s %-19s\n",
            $task['id'],
            $status,
            $desc,
            $task['created_at']
        );
    }
    echo str_repeat('-', 60) . "\n";
}

function markDone(int $id): void
{
    $tasks = loadTasks();
    $found = false;

    foreach ($tasks as &$task) {
        if ($task['id'] === $id) {
            if ($task['done']) {
                echo "Task #{$id} is already done.\n";
                return;
            }
            $task['done'] = true;
            $found = true;
            break;
        }
    }
    unset($task);

    if (!$found) {
        echo "Error: Task #{$id} not found.\n";
        exit(1);
    }

    saveTasks($tasks);
    echo "Task #{$id} marked as done.\n";
}

function deleteTask(int $id): void
{
    $tasks = loadTasks();
    $before = count($tasks);

    $tasks = array_values(
        array_filter($tasks, fn($t) => $t['id'] !== $id)
    );

    if (count($tasks) === $before) {
        echo "Error: Task #{$id} not found.\n";
        exit(1);
    }

    saveTasks($tasks);
    echo "Task #{$id} deleted.\n";
}

function searchTasks(string $term): void
{
    $tasks = loadTasks();
    $term = strtolower(trim($term));

    if ($term === '') {
        echo "Search term cannot be empty.\n";
        return;
    }

    $results = array_filter(
        $tasks,
        fn($t) => strpos(strtolower($t['description']), $term) !== false
    );

    if (empty($results)) {
        echo "No tasks match \"{$term}\".\n";
        return;
    }

    echo "Found " . count($results) . " task(s) matching \"{$term}\":\n\n";
    foreach ($results as $task) {
        $status = $task['done'] ? '[Done]' : '[  ]';
        echo "  #{$task['id']} {$status} {$task['description']} ({$task['created_at']})\n";
    }
}

function printUsage(): void
{
    echo "Usage: php todo.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  add <description>    Add a new task\n";
    echo "  list [filter]       List tasks (filter: done|pending)\n";
    echo "  done <id>           Mark task as done\n";
    echo "  delete <id>         Delete a task\n";
    echo "  search <term>       Search tasks by description\n";
    echo "  help                Show this help message\n";
}
