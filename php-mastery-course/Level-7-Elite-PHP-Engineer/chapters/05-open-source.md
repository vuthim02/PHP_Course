# Chapter 5: Open Source Leadership

## Learning Objectives

- Build open source communities
- Maintain popular PHP packages
- Handle issues and contributions
- Create sustainable projects

---

## 5.1 Open Source Best Practices

```markdown
# CONTRIBUTING.md

## Getting Started

1. Fork the repository
2. Create a feature branch
3. Write tests
4. Submit a pull request

## Code Style

- PSR-12 coding standard
- PHPStan level max
- 100% test coverage for new code

## Pull Request Process

1. Ensure tests pass
2. Update documentation
3. Add changelog entry
4. Request review from maintainers

## Community Guidelines

- Be respectful and inclusive
- Focus on constructive feedback
- Help new contributors

---

# CODE_OF_CONDUCT.md

## Our Pledge

We pledge to make participation in our project
a harassment-free experience for everyone.

## Our Standards

- Using welcoming language
- Being respectful of differing viewpoints
- Accepting constructive criticism
- Focusing on what's best for the community

## Enforcement

Project maintainers are responsible for clarifying
and enforcing our standards.
```

### Maintenance Workflow

```yaml
# .github/workflows/maintain.yml
name: Maintain

on:
  schedule:
    - cron: '0 8 * * 1'  # Every Monday

jobs:
  stale:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/stale@v8
        with:
          days-before-stale: 60
          days-before-close: 14
          stale-issue-label: stale
          stale-issue-message: 'This issue is stale. Please update or it will be closed.'

  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: composer audit
```

---

## 5.2 Exercises

1. Create a CONTRIBUTING.md for a project
2. Review and merge 3 pull requests
3. Handle a difficult issue or bug report professionally
4. Set up automated maintenance workflows

---

## Further Reading

- **Doc:** [Open Source Guides](https://opensource.guide/)
- **Doc:** [Semantic Versioning](https://semver.org/)
