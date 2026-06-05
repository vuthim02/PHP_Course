# Chapter 16: Consulting and Entrepreneurship

## Learning Objectives

- Start a PHP consulting business
- Structure engagements and contracts
- Build a product company
- Scale consulting practice

---

## 16.1 Consulting Framework

```php
<?php
class ConsultingEngagement
{
    public function __construct(
        private string $client,
        private string $scope,
        private string $duration,
        private float $rate,
    ) {}

    public function phases(): array
    {
        return [
            'discovery' => [
                'duration' => '1-2 weeks',
                'activities' => [
                    'Stakeholder interviews',
                    'Codebase review',
                    'Architecture assessment',
                    'Identify pain points',
                ],
                'deliverables' => ['Current state report', 'Initial recommendations'],
            ],
            'planning' => [
                'duration' => '1 week',
                'activities' => [
                    'Create roadmap',
                    'Define success metrics',
                    'Prioritize initiatives',
                    'Resource planning',
                ],
                'deliverables' => ['Roadmap document', 'Project plan'],
            ],
            'execution' => [
                'duration' => '4-12 weeks',
                'activities' => [
                    'Architecture improvements',
                    'Code refactoring',
                    'Team mentoring',
                    'Best practices implementation',
                ],
                'deliverables' => ['Improved codebase', 'Documentation', 'Team training'],
            ],
            'handoff' => [
                'duration' => '1-2 weeks',
                'activities' => [
                    'Knowledge transfer',
                    'Documentation',
                    'Success criteria review',
                ],
                'deliverables' => ['Handoff document', 'Follow-up plan'],
            ],
        ];
    }
}

// Contract template
// CONSULTING SERVICES AGREEMENT
//
// 1. Services: Architecture review, code quality improvement, team mentoring
// 2. Duration: 12 weeks starting [date]
// 3. Hours: 20 hours/week
// 4. Rate: $200/hour
// 5. Payment: Net 30
// 6. IP: Client owns all work product
// 7. Confidentiality: Standard NDA applies
// 8. Termination: 2 weeks notice by either party

class Proposal
{
    public function __construct(
        private string $client,
        private string $problem,
        private string $solution,
        private array $deliverables,
        private float $investment,
        private string $timeline,
    ) {}

    public function generate(): string
    {
        return <<<PROPOSAL
# Proposal for {$this->client}

## Problem
{$this->problem}

## Solution
{$this->solution}

## Deliverables
- {$this->formatList($this->deliverables)}

## Investment
\${$this->investment}

## Timeline
{$this->timeline}
PROPOSAL;
    }

    private function formatList(array $items): string
    {
        return implode("\n- ", $items);
    }
}
```

---

## 16.2 Exercises

1. Create a services menu with fixed-price packages
2. Draft a consulting contract and SOW template
3. Build a lead generation strategy (content, speaking, referrals)
4. Create a business plan for a PHP product company

---

## Further Reading

- **Book:** "The Consulting Bible" by Alan Weiss
- **Book:** "Million Dollar Consulting" by Alan Weiss
