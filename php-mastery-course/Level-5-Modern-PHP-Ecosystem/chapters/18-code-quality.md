# Chapter 18: Code Quality Tools

## Learning Objectives

- Use PHP-CS-Fixer for code style
- Configure PHP CodeSniffer
- Automate code quality with Rector
- Enforce quality in CI/CD

---

## 18.1 Tool Configuration

```php
<?php
// .php-cs-fixer.dist.php
$finder = PhpCsFixer\Finder::create()
    ->in(['src/', 'tests/'])
    ->exclude(['vendor', 'var']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_align' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);

// phpcs.xml
// <?xml version="1.0"?>
// <ruleset name="Project Standard">
//     <file>src/</file>
//     <file>tests/</file>
//     <rule ref="PSR12"/>
//     <rule ref="Generic.PHP.ForbiddenFunctions">
//         <properties>
//             <property name="forbiddenFunctions" type="array">
//                 <element key="var_dump" value="null"/>
//                 <element key="dd" value="null"/>
//                 <element key="dump" value="null"/>
//             </property>
//         </properties>
//     </rule>
// </ruleset>

// rector.php
use Rector\Config\RectorConfig;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets()
    ->withAttributesSets()
    ->withRules([
        SimplifyIfElseToTernaryRector::class,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
    );

// composer.json scripts
"scripts": {
    "cs-check": "php-cs-fixer fix --dry-run",
    "cs-fix": "php-cs-fixer fix",
    "sniff": "phpcs",
    "fix-all": "phpcbf",
    "refactor": "rector",
    "quality": ["@cs-check", "@sniff", "phpstan analyse --level=max"]
}
```

---

## 18.2 Exercises

1. Configure PHP-CS-Fixer and format a codebase
2. Install and run PHP CodeSniffer with custom rules
3. Use Rector to upgrade PHP 7.4 code to PHP 8.2
4. Add all quality tools to a CI pipeline

---

## Further Reading

- **Doc:** [PHP-CS-Fixer](https://cs.symfony.com/)
- **Doc:** [Rector](https://getrector.com/)
