# Chapter 18: Technical Writing

## Learning Objectives

- Write technical blog posts
- Author books and tutorials
- Create documentation
- Build a technical audience

---

## 18.1 Technical Writing Framework

```php
<?php
class TechnicalArticle
{
    public function __construct(
        private string $title,
        private string $topic,
        private string $audience,
    ) {}

    public function outline(): array
    {
        return [
            'hook' => $this->createHook(),
            'problem' => 'What problem are we solving?',
            'solution' => 'Step-by-step implementation',
            'code' => 'Complete working examples',
            'explanation' => 'Why this approach works',
            'alternatives' => 'Other approaches considered',
            'summary' => 'Key takeaways and next steps',
        ];
    }

    public function createHook(): string
    {
        $hooks = [
            'statistic' => "Did you know 80% of PHP applications have SQL injection vulnerabilities?",
            'story' => "Last month, we reduced our API response time from 2s to 50ms...",
            'question' => "Have you ever wondered why your PHP application slows down under load?",
            'myth' => "You think PHP can't handle real-time applications? Think again.",
        ];
        return $hooks[array_rand($hooks)];
    }

    public function seoMetadata(): array
    {
        return [
            'title' => $this->title,
            'description' => "Learn how to {$this->topic} with PHP. Complete guide with code examples.",
            'tags' => ['php', $this->topic, 'web development', 'tutorial'],
            'canonical_url' => "https://example.com/posts/" . $this->slugify($this->title),
        ];
    }

    private function slugify(string $title): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    }
}

// Documentation template
interface Documentation
{
    public function getTitle(): string;
    public function getDescription(): string;
    public function getInstallation(): string;
    public function getUsage(): string;
    public function getExamples(): array;
    public function getApiReference(): array;
    public function getTroubleshooting(): array;
}

class PackageReadme implements Documentation
{
    public function __construct(
        private string $packageName,
        private string $description,
        private array $installationSteps,
        private array $usageExamples,
    ) {}

    public function getTitle(): string
    {
        return "# {$this->packageName}";
    }

    public function getInstallation(): string
    {
        return "## Installation\n\n```bash\ncomposer require {$this->packageName}\n```";
    }

    public function getUsage(): string
    {
        $examples = '';
        foreach ($this->usageExamples as $title => $code) {
            $examples .= "### {$title}\n\n```php\n{$code}\n```\n\n";
        }
        return "## Usage\n\n{$examples}";
    }

    public function getExamples(): array
    {
        return $this->usageExamples;
    }

    public function getApiReference(): array
    {
        return [
            'Classes' => [],
            'Methods' => [],
            'Interfaces' => [],
        ];
    }

    public function getTroubleshooting(): array
    {
        return [
            'Common issues' => [],
            'FAQ' => [],
        ];
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function generate(): string
    {
        return implode("\n\n", [
            "# {$this->packageName}",
            "{$this->description}",
            '## Status',
            '![Build](https://github.com/author/package/actions/workflows/ci.yml/badge.svg)',
            '![Packagist](https://img.shields.io/packagist/dt/author/package)',
            '![PHP Version](https://img.shields.io/packagist/php-v/author/package)',
            $this->getInstallation(),
            $this->getUsage(),
            '## Testing',
            "```bash\ncomposer test\n```",
            '## Contributing',
            'See CONTRIBUTING.md',
            '## License',
            'MIT',
        ]);
    }
}
```

---

## 18.2 Exercises

1. Write a technical blog post with working code examples
2. Create comprehensive README for a PHP package
3. Start a technical blog on a platform (dev.to, Medium, personal site)
4. Write a tutorial with step-by-step instructions

---

## Further Reading

- **Doc:** [Google Technical Writing Course](https://developers.google.com/tech-writing)
- **Book:** "Writing Well" by William Zinsser
