# Chapter 7: Team Leadership

## Learning Objectives

- Mentor junior and mid-level engineers
- Conduct effective code reviews
- Manage engineering performance
- Build high-performing teams

---

## 7.1 Code Review Guidelines

```php
<?php
// ❌ Code that needs review feedback
class DataProcessor
{
    public function process($data)
    {
        $result = [];
        foreach ($data as $k => $v) {
            if ($v > 0) {
                $result[] = $v * 2;
            }
        }
        return $result;
    }
}

// ✅ Code review feedback
// 1. Add type hints for parameters and return
// 2. Use more descriptive variable names
// 3. Consider extracting the transformation logic
// 4. Add PHPDoc for the method
// 5. Write unit tests before merging

class DataProcessor
{
    /**
     * Filters positive values and doubles them.
     *
     * @param int[] $values Input array of integers
     * @return int[] Filtered and transformed values
     */
    public function process(array $values): array
    {
        return array_map(
            fn(int $value): int => $value * 2,
            array_filter($values, fn(int $value): bool => $value > 0)
        );
    }
}

// 1-on-1 meeting template
class OneOnOne
{
    private array $topics = [
        'achievements' => [
            'What went well this week?',
            'What are you proud of?',
        ],
        'challenges' => [
            'What\'s blocking you?',
            'What could be improved?',
        ],
        'growth' => [
            'What do you want to learn?',
            'What skills should you develop?',
        ],
        'feedback' => [
            'How can I support you better?',
            'What should I start/stop/continue?',
        ],
    ];

    public function conduct(string $engineerName): array
    {
        $notes = [];
        // Run through each topic
        return $notes;
    }
}
```

---

## 7.2 Exercises

1. Conduct a code review for a complex pull request
2. Prepare and lead a tech talk for your team
3. Create a growth plan for a junior engineer
4. Handle a performance improvement conversation

---

## Further Reading

- **Book:** "The Manager's Path" by Camille Fournier
- **Book:** "An Elegant Puzzle" by Will Larson
