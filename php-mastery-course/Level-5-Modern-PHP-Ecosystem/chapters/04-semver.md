# Chapter 4: Semantic Versioning

## Learning Objectives

- Apply semantic versioning to PHP projects
- Manage breaking changes
- Generate changelogs
- Use version constraints in Composer

---

## 4.1 Version numbering

```text
MAJOR.MINOR.PATCH

1.4.2 → Major: 1, Minor: 4, Patch: 2

Patch (1.4.2 → 1.4.3):
- Bug fixes
- Backward compatible

Minor (1.4.2 → 1.5.0):
- New features
- Backward compatible
- May deprecate old features

Major (1.4.2 → 2.0.0):
- Breaking changes
- Incompatible API changes

Pre-release: 1.0.0-alpha.1, 2.0.0-beta.2, 3.0.0-rc.1
Build metadata: 1.0.0+build.2024-01-15
```

---

## 4.2 Exercises

1. Apply semver to a PHP library with proper tagging
2. Write a CHANGELOG following Keep a Changelog format
3. Handle deprecation notices between minor versions
4. Create an upgrade guide between major versions

---

## Further Reading

- **Doc:** [Semver.org](https://semver.org/)
- **Doc:** [Keep a Changelog](https://keepachangelog.com/)
