# Chapter 17: Conference Speaking

## Learning Objectives

- Write compelling CFP proposals
- Prepare and deliver talks
- Handle Q&A sessions
- Build speaker reputation

---

## 17.1 CFP Writing

```markdown
# CFP Proposal Template

## Title
From Legacy to Modern: Migrating a 10-Year-Old PHP Codebase

## Format
- 40-minute talk
- Intermediate/Advanced level

## Abstract
In 2023, we migrated a 10-year-old PHP 5.6 monolith with 500K+ lines
of code to PHP 8.3 with modern architecture. This talk covers:
- Assessment and strategy
- Incremental migration approach
- Tools and automation
- Team coordination
- Lessons learned

## Audience Takeaways
1. A proven strategy for migrating legacy PHP
2. Tools for automated code transformation
3. How to maintain business continuity during migration
4. Common pitfalls and how to avoid them

## Speaker Bio
Jane Doe is a Staff Engineer at Acme Corp with 12+ years of PHP
experience. She maintains several open source packages and is
a contributor to PHP-FIG.

## Previous Talks
- PHP Conference 2023: "Taming the Monolith"
- Laracon 2022: "Scaling Laravel to 1M+ Users"
- Local PHP Meetup: Regular speaker (10+ talks)

## Requirements
- Projector with HDMI
- Wireless microphone
- Stage monitor
```

---

## 17.2 Talk Preparation

```php
<?php
class TalkPreparer
{
    private array $checklist = [
        '3 months before' => [
            'Submit CFP',
            'Outline talk structure',
            'Start slide deck',
        ],
        '1 month before' => [
            'Complete first draft of slides',
            'Write speaker notes',
            'Create demo environment',
        ],
        '2 weeks before' => [
            'Dry run with team',
            'Time the talk',
            'Refine based on feedback',
            'Backup slides to cloud',
        ],
        '1 week before' => [
            'Practice daily',
            'Prepare Q&A answers',
            'Pack adapters, clicker, cables',
        ],
        'day of' => [
            'Arrive early to test setup',
            'Walk the stage',
            'Breathe and enjoy',
            'Record the talk',
        ],
    ];

    private array qaPreparation = [
        'How long did the migration take?' => '18 months total, 12 months active work',
        'What would you do differently?' => 'Start with test coverage before refactoring',
        'Did you lose any features?' => 'No, we maintained feature parity throughout',
        'How did you handle team resistance?' => 'Involved team in decisions, showed quick wins',
    ];
}

// Presentation tips
// 1. Start with a hook (problem, story, statistic)
// 2. Use the rule of three (3 main points)
// 3. Show live demos
// 4. Include code snippets
// 5. Have a clear call-to-action
// 6. End with Q&A
// 7. Share slides and resources afterwards
```

---

## 17.3 Exercises

1. Write a CFP proposal for your area of expertise
2. Create a talk outline and slide deck
3. Give a practice talk to colleagues and collect feedback
4. Submit to at least 2 conferences

---

## Further Reading

- **Doc:** [CFP Resources](https://www.cfphelp.com/)
- **Book:** "Confessions of a Public Speaker" by Scott Berkun
