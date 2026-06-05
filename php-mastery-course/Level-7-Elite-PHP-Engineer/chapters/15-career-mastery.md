# Chapter 15: Career Mastery

## Learning Objectives

- Navigate career paths (Staff, Principal, CTO)
- Build personal brand
- Negotiate compensation
- Lead technical organizations

---

## 15.1 Career Paths

```php
<?php
class CareerPath
{
    public static function getPaths(): array
    {
        return [
            'individual_contributor' => [
                'Junior Engineer' => ['years' => '0-2', 'focus' => 'Learning, implementation'],
                'Engineer' => ['years' => '2-4', 'focus' => 'Ownership, delivery'],
                'Senior Engineer' => ['years' => '4-7', 'focus' => 'Architecture, mentoring'],
                'Staff Engineer' => ['years' => '7-10', 'focus' => 'Cross-team impact, strategy'],
                'Principal Engineer' => ['years' => '10-15', 'focus' => 'Org-wide technical vision'],
                'Fellow/Distinguished' => ['years' => '15+', 'focus' => 'Industry influence'],
            ],
            'management' => [
                'Tech Lead' => ['years' => '3-5', 'focus' => 'Team technical direction'],
                'Engineering Manager' => ['years' => '5-8', 'focus' => 'People management, delivery'],
                'Senior Manager' => ['years' => '8-12', 'focus' => 'Multi-team leadership'],
                'Director of Engineering' => ['years' => '12-15', 'focus' => 'Org strategy'],
                'VP of Engineering' => ['years' => '15+', 'focus' => 'Company-wide engineering'],
                'CTO' => ['years' => '15+', 'focus' => 'Technology vision, business alignment'],
            ],
        ];
    }
}

// Staff Engineer expectations
class StaffEngineer
{
    public function responsibilities(): array
    {
        return [
            'Technical Strategy' => 'Drive architecture decisions across teams',
            'Mentoring' => 'Develop senior engineers into leaders',
            'Cross-team Impact' => 'Lead initiatives spanning multiple teams',
            'Code Quality' => 'Set standards for the entire organization',
            'Communication' => 'Write tech specs, present to leadership',
            'Recruiting' => 'Interview senior candidates, improve hiring',
        ];
    }

    public function skills(): array
    {
        return [
            'Deep technical expertise in PHP ecosystem',
            'System design and architecture',
            'Communication and influence without authority',
            'Business acumen and product thinking',
            'Conflict resolution and negotiation',
            'Strategic thinking and long-term planning',
        ];
    }
}
```

---

## 15.2 Exercises

1. Create a 5-year career development plan
2. Build a personal brand (blog, talks, open source)
3. Practice negotiation scenarios (salary, title, resources)
4. Prepare a Staff Engineer promotion packet

---

## Further Reading

- **Book:** "Staff Engineer" by Will Larson
- **Book:** "The Software Engineer's Guidebook" by Gergely Orosz
