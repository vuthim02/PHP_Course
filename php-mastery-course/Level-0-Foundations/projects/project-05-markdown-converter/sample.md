# Markdown to HTML Converter

This is a **sample document** for testing the Markdown converter. It demonstrates every supported feature.

## Text Formatting

This paragraph shows *italic text*, **bold text**, and `inline code`. You can also have ~~strikethrough~~ (bonus) and **_combined_** styles.

## Links and Images

Here's a link to [PHP's official site](https://php.net).

## Lists

### Unordered List

- PHP
- JavaScript
- Go
  - Goroutines (nested)
  - Channels
- Rust

### Ordered List

1. First item
2. Second item
3. Third item

## Code Blocks

A code block with no language:

```
function greet($name) {
    return "Hello, $name!";
}
```

A code block with PHP language specified:

```php
<?php

declare(strict_types=1);

class HelloWorld
{
    public function greet(string $name): string
    {
        return sprintf('Hello, %s!', $name);
    }
}
```

And some inline `code` within a paragraph.

## Blockquotes

> This is a blockquote. It can span multiple lines.
>
> Blockquotes can contain **formatting** too.

> Nested blockquotes are also possible:
> > Like this one!

## Horizontal Rules

---

Above this line is a horizontal rule.

## Mixed Content

Here's a paragraph with a [link to GitHub](https://github.com), an image placeholder, and some `code`.

> "Any sufficiently advanced technology is indistinguishable from magic."
> — Arthur C. Clarke

1. Learn Markdown
2. Convert to HTML
3. ...

---

*Happy coding!*
