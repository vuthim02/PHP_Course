# Project 5: Markdown to HTML Converter

A **from-scratch Markdown parser** written in PHP CLI. Reads a `.md` file and produces a complete HTML document. Supports headings, bold, italic, links, images, code blocks, lists, blockquotes, horizontal rules, and inline code — all without any third-party libraries.

## Learning Objectives

- Understand **parsing fundamentals**: tokenizing lines, detecting patterns, building output
- Practice **string manipulation** in PHP: `str_starts_with`, `preg_replace`, `sprintf`
- Implement a **recursive parsing** pattern (blockquotes contain inline elements)
- Design a **plugin-free architecture** — no Composer, no libraries, pure PHP
- Use **regular expressions** for inline pattern matching (links, images, bold, italic)
- Build a **template system** with placeholder substitution (`{{TITLE}}`, `{{BODY}}`)
- Master **PHP CLI** argument parsing (`$argv`, `--flags`)

## Features

| Feature | Markdown Syntax | HTML Output |
|---------|----------------|-------------|
| **Headings** | `# H1` through `###### H6` | `<h1>` – `<h6>` |
| **Bold** | `**text**` or `__text__` | `<strong>` |
| **Italic** | `*text*` or `_text_` | `<em>` |
| **Links** | `[text](url)` | `<a href="url">` |
| **Images** | `![alt](url)` | `<img src="url" alt="">` |
| **Inline Code** | `` `code` `` | `<code>` |
| **Code Blocks** | ```` ``` ```` or `~~~` with optional language | `<pre><code class="language-...">` |
| **Unordered Lists** | `- item`, `* item`, `+ item` | `<ul><li>` |
| **Ordered Lists** | `1. item` | `<ol><li>` |
| **Blockquotes** | `> quote` | `<blockquote>` |
| **Horizontal Rules** | `---`, `***`, `___` | `<hr>` |
| **Paragraphs** | Blank-line-separated text blocks | `<p>` |

## How to Run

```bash
cd project-05-markdown-converter

# Convert sample.md and print HTML to stdout
php convert.php sample.md

# Convert to an output file
php convert.php sample.md output.html

# Use a custom HTML template
php convert.php sample.md --template template.html

# Full: input, output, and template
php convert.php sample.md output.html --template template.html

# Show help
php convert.php --help
```

### Serve the Output

```bash
php convert.php sample.md output.html

# View in browser
php -S localhost:8080
# Then open http://localhost:8080/output.html
```

## Expected Output

Running `php convert.php sample.md` produces a fully styled HTML page (`<h1>`–`<h6>`, `<ul>`, `<ol>`, `<pre><code>`, `<blockquote>`, `<hr>`, `<p>`, `<a>`, `<img>`, `<strong>`, `<em>`, `<code>`) rendered in a serif-themed template with:

- **Styled headings** with bottom borders on H1
- **Syntax-highlighted code blocks** (dark background, monospace)
- **Purple inline code** (`#c026d3`)
- **Blockquotes** with indigo left border and light background
- **Responsive layout** (max-width 720px, centered)

The `sample.md` file exercises every supported feature so you can visually verify correctness.

## Code Structure

```
project-05-markdown-converter/
├── convert.php       # Main CLI script — parser + template engine
├── sample.md         # Sample Markdown file (exercises all features)
├── template.html     # Customizable HTML template with {{TITLE}} and {{BODY}}
└── README.md
```

### File Responsibilities

- **`convert.php`** — Contains two classes:
  - `MarkdownParser`: The core parser. `parse()` processes block elements line-by-line. `parseInline()` handles inline elements with regex. Helper methods (`isCodeFence`, `parseHeading`, `isUnorderedListItem`, etc.) detect block types.
  - `TemplateEngine`: Wraps the generated HTML body in a full document with `{{TITLE}}` and `{{BODY}}` substitution.
  - CLI entry point: parses `$argv` for input file, optional output file, and `--template` / `--help` flags.

- **`sample.md`** — Comprehensive Markdown file demonstrating all supported syntax. Used for testing and as a reference.

- **`template.html`** — A standalone HTML template with serif typography, code block dark mode styling, and responsive design. Override with `--template <path>`.

### How the Parser Works

1. Split the Markdown text into lines
2. Iterate line-by-line, detecting **block-level elements** (headings, code fences, lists, quotes, HRs)
3. For each block element, parse the content and emit HTML
4. **Inline parsing** (`parseInline`) runs on text content: escapes HTML, then applies regex replacements for bold, italic, code, links, and images
5. Blockquotes call back into `parse()` recursively to handle nested Markdown

## Concepts Practiced

| Concept | How It's Used |
|---------|---------------|
| **How computers work** | Text encoding (UTF-8), character escaping, data transformation pipeline |
| **PHP CLI** | `$argv`, STDERR/STDOUT, exit codes, command-line flags |
| **Strings & regex** | `str_starts_with`, `str_contains`, `preg_replace`, `preg_match`, `sprintf` |
| **File I/O** | `file_get_contents`, `file_put_contents`, `is_file`, `pathinfo` |
| **Algorithms** | Line-by-line parsing, state machine (in/out of code block), recursion |
| **HTML** | Semantic document structure, inline vs block elements, escaping |
| **Templating** | Placeholder substitution, separation of content from presentation |
| **Markdown** | Understanding one of the most widely used markup languages |
