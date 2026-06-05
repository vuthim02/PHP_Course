# Chapter 9: SPL Data Structures

## Learning Objectives

- Use SplStack, SplQueue, SplHeap
- Implement priority queues
- Use SplFixedArray for performance
- Understand when to use each structure

---

## 9.1 Stack and Queue

```php
<?php
// SplStack (LIFO — Last In, First Out)
$stack = new SplStack();
$stack->push('first');
$stack->push('second');
$stack->push('third');

echo $stack->pop();  // third
echo $stack->pop();  // second
echo $stack->pop();  // first

// SplQueue (FIFO — First In, First Out)
$queue = new SplQueue();
$queue->enqueue('job1');
$queue->enqueue('job2');
$queue->enqueue('job3');

echo $queue->dequeue();  // job1
echo $queue->dequeue();  // job2

// Queue with blocking behavior simulation
class JobQueue
{
    private SplQueue $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    public function dispatch(Job $job): void
    {
        $this->queue->enqueue($job);
    }

    public function process(): void
    {
        while (!$this->queue->isEmpty()) {
            $job = $this->queue->dequeue();
            $job->handle();
        }
    }

    public function count(): int
    {
        return $this->queue->count();
    }
}
```

---

## 9.2 Heap and Priority Queue

```php
<?php
// MaxHeap (largest value first)
class MaxHeap extends SplMaxHeap
{
    public function compare(mixed $a, mixed $b): int
    {
        return $a['priority'] <=> $b['priority'];
    }
}

$heap = new MaxHeap();
$heap->insert(['task' => 'Low priority', 'priority' => 1]);
$heap->insert(['task' => 'Critical', 'priority' => 10]);
$heap->insert(['task' => 'Medium', 'priority' => 5]);

foreach ($heap as $item) {
    echo $item['task'] . "\n";
}
// Critical
// Medium
// Low priority

// SplPriorityQueue
class TaskQueue extends SplPriorityQueue
{
    public function compare(mixed $p1, mixed $p2): int
    {
        return $p1 <=> $p2;
    }
}

$queue = new TaskQueue();
$queue->insert('Send newsletter', 1);
$queue->insert('Fix critical bug', 10);
$queue->insert('Update docs', 3);

$queue->setExtractFlags(SplPriorityQueue::EXTR_DATA);
foreach ($queue as $task) {
    echo $task . "\n";
}
```

---

## 9.3 Exercises

1. Implement an undo system using SplStack
2. Build a job queue using SplQueue with worker simulation
3. Use SplPriorityQueue for a task scheduler
4. Compare memory usage of SplFixedArray vs regular array with 1M elements

---

## Further Reading

- **Doc:** [SPL Data Structures](https://www.php.net/manual/en/spl.datastructures.php)
