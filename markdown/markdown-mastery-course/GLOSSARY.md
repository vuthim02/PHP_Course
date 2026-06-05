# Glossary of Terms

> Over 100 essential terms for Markdown and documentation engineering.

---

## A

### Abstract Syntax Tree (AST)
A tree representation of the syntactic structure of source code. Markdown parsers convert raw text into an AST before rendering. Tools like remark and mdast work with Markdown ASTs. *See also: Parser, Renderer, Tokenization*

### Accessibility (a11y)
The practice of making documentation usable by people with disabilities. Includes proper heading hierarchies, alt text on images, semantic HTML, color contrast, and screen reader support. *See also: Alt Text, Semantic HTML*

### ADR (Architecture Decision Record)
A document that captures an important architectural decision made along with its context and consequences. ADRs are typically written in Markdown and stored in the project repository. *See also: Documentation as Code*

### Alexandria
A modern documentation platform that uses Markdown files stored in a Git repository. Focuses on developer experience and team collaboration. *See also: Docusaurus, MkDocs, ReadTheDocs*

### Alt Text (Alternative Text)
Text description added to images via the `![alt text](url)` syntax. Essential for accessibility and displays when images fail to load. *See also: Accessibility*

### Antora
A multi-repository documentation site generator for AsciiDoc. While it primarily uses AsciiDoc, it influenced the Markdown documentation ecosystem with its component-based architecture. *See also: Documentation Site, Static Site Generator*

### API Documentation
Technical documentation that describes how to use an API (Application Programming Interface). Often generated from OpenAPI/Swagger specifications and rendered with Markdown. *See also: OpenAPI, Swagger*

### AsciiDoc
A text-based markup language similar to Markdown but more powerful and strict. Used by Antora and AsciiDoctor. Supports attributes, macros, and includes natively. *See also: Markdown Comparison*

### AST (Abstract Syntax Tree)
*See: Abstract Syntax Tree*

---

## B

### Backtick
The `` ` `` character used for inline code spans and fenced code blocks. Single backticks for inline code, triple backticks for code blocks.

### Badge
A small image badge displaying metadata like build status, version, license, or coverage. Created with shields.io and embedded using Markdown image syntax. *See also: Shields.io*

### Block Element
A Markdown element that occupies its own block, such as paragraphs, headings, lists, blockquotes, code blocks, and horizontal rules. *See also: Inline Element*

### Blockquote
A Markdown element created with `>` that indicates quoted or highlighted text. Supports nesting (`>>`), multiple paragraphs, and embedded elements. *See also: Callout, Note*

### Bold
Text rendered in bold using `**double asterisks**` or `__double underscores__`. *See also: Emphasis, Italic, Strong*

### Bookdown
An R package for writing books and long-form documents with Markdown. Extends RMarkdown with multi-chapter book support, cross-referencing, and multiple output formats. *See also: RMarkdown, Pandoc*

### Bracketed Spans
An extension in some Markdown flavors (like Pandoc and CommonMark) for adding attributes to inline elements using `[text]{.class #id}` syntax. *See also: Fenced Divs*

---

## C

### Callout
A highlighted block of text used to draw attention to notes, warnings, tips, or important information. Implemented via blockquotes with special prefixes like `> [!NOTE]`, `> [!WARNING]`, `> [!TIP]`, `> [!CAUTION]`, `> [!IMPORTANT]`. Native to Obsidian, GitHub, and some MDX processors. *See also: Blockquote, Note*

### CamelCase
A writing convention where compound words are concatenated with each word starting with a capital letter (e.g., `WikiWord`). Used by some early wiki systems and Markdown implementations for automatic linking. *See also: Wiki Link*

### Changelog
A curated, chronologically ordered list of notable changes for a project. The "Keep a Changelog" convention uses Markdown with YYYY-MM-DD dates and semantic versioning. *See also: KEEPaChangelog, Semantic Versioning*

### CI/CD (Continuous Integration / Continuous Deployment)
Automated pipelines that build, test, and deploy documentation. Common tools: GitHub Actions, GitLab CI, CircleCI. Essential for Docs as Code workflows. *See also: Documentation as Code, GitHub Actions*

### Code Block
A block of preformatted code text. Created with fenced triple backticks ` ```language ` or indented with 4 spaces. Supports syntax highlighting with language specifiers. *See also: Backtick, Fenced Code Block, Syntax Highlighting*

### Code Span
Inline code created with single backticks (\`code\`). Used for variable names, commands, file paths, and short code snippets. *See also: Code Block, Inline Element*

### CommonMark
A standardized, unambiguous specification of Markdown, created to resolve the fragmentation caused by different Markdown implementations. Version 0.31.2 is the latest. Most modern Markdown tools aim for CommonMark compliance. *See also: GFM, Markdown Flavors*

### Contributing Guide (CONTRIBUTING.md)
A document that explains how others can contribute to a project. Includes setup instructions, coding standards, pull request process, and review guidelines. *See also: README, TEMPLATES*

### Cross-Reference
A link pointing to another section within the same document or documentation set. In Markdown, created using anchor links: `[text](#section-id)`. *See also: Internal Link, Anchor*

---

## D

### Definition List
A list that pairs terms with their definitions. Created using `Term` on one line followed by `: Definition` on the next. Supported by GFM and some other flavors. *See also: List, GFM*

### Digital Garden
A personal knowledge management system, typically built with Markdown, where notes are interconnected, grow organically, and are publicly published. Tools: Obsidian, Foam, Gatsby Digital Garden. *See also: Knowledge Base, Obsidian, Zettelkasten*

### Docs as Code (DaC)
A philosophy that treats documentation with the same rigor as code: version control, code review, automated testing, CI/CD, and agile methodologies. *See also: CI/CD, Documentation Engineering, Git*

### Documentation Engineering
The discipline of designing, building, and maintaining documentation systems using software engineering practices. Combines technical writing, software development, and DevOps. *See also: Docs as Code, Technical Writer*

### Documentation Site
A website that hosts documentation, typically generated from Markdown files by a static site generator. Examples: docs.example.com, readthedocs.io. *See also: Docusaurus, MkDocs, ReadTheDocs*

### Docusaurus
A static site generator by Meta, built specifically for documentation. Features versioning, i18n, search, blog, MDX support, and React-based theming. Uses Markdown as its primary content format. *See also: MDX, React, Static Site Generator*

### DocUtils
A Python-based documentation utility library. Part of the Docutils project which includes reStructuredText processing tools. *See also: reStructuredText*

### Draft
A document marked as a work-in-progress. In static site generators, controlled via front matter: `draft: true`. Drafts are not included in production builds. *See also: Front Matter, Published*

---

## E

### ECMA-376
The Office Open XML (OOXML) standard that defines .docx format. Relevant for Markdown-to-Word conversion via Pandoc. *See also: Pandoc*

### Editor
A software application used to write Markdown. Popular choices: VS Code, Obsidian, Typora, iA Writer, Sublime Text, Logseq, Notion. *See also: IDE, Obsidian, Typora, VS Code*

### Emoji
Small digital images or icons used to express ideas or emotions. In Markdown, inserted via `:emoji_code:` syntax (e.g., `:rocket:` for 🚀). GitHub and many other platforms support emoji shortcodes. *See also: GFM*

### Emphasis
Text formatting that adds stress or importance. In Markdown, italic is `*single asterisks*` and bold is `**double asterisks**`. *See also: Bold, Italic, Strong*

### Entity Relationship Diagram (ERD)
A diagram that illustrates how entities relate to each other in a system. Supported in Mermaid using the `erDiagram` keyword. *See also: Mermaid, Diagram*

### Escape Character
The backslash `\` used to render special Markdown characters literally. For example, `\*` renders as a literal asterisk instead of starting emphasis. *See also: Special Characters*

### Extension
A plugin or add-on that extends Markdown capabilities. Examples: markdown-it plugins, remark plugins, Python-Markdown extensions, MkDocs plugins. *See also: Plugin, remark*

---

## F

### Fenced Code Block
A code block delimited by triple backticks ` ``` ` or triple tildes `~~~`. Supports language specifiers for syntax highlighting. Unlike indented code blocks, fenced blocks allow blank lines within the code. *See also: Code Block, Indented Code Block, Syntax Highlighting*

### Fenced Div
A block-level container created with `:::div-name` fences. Supported by Pandoc and some extended Markdown processors. Used for custom containers, warnings, and styled blocks. *See also: Bracketed Spans, Callout*

### Flavor
*See: Markdown Flavor*

### Flowchart
A diagram representing a workflow or process. Created in Mermaid using the `flowchart` or `graph` keyword. Supports different node shapes, link styles, and subgraphs. *See also: Mermaid, Diagram*

### Font Awesome
An icon library often used in documentation sites. Icons can be embedded in Markdown via HTML: `<i class="fas fa-rocket"></i>` or through SSG theme configurations. *See also: Badge, Emoji*

### Footnote
A note placed at the bottom of a page that provides additional information about a reference in the text. Created with `[^label]` inline and `[^label]: content` at the bottom. Supported in GFM, MultiMarkdown, Pandoc. *See also: GFM, MultiMarkdown*

### Front Matter
Metadata at the top of a Markdown file, delimited by `---`. Written in YAML, TOML, or JSON. Contains fields like title, date, tags, categories, draft status, and custom data used by SSGs. *See also: YAML, TOML, JSON, Metadata*

### FSH (Feature Specification Hierarchies)
A notation for specifying product features, often used in technical documentation alongside Markdown for structured product requirements. *See also: Technical Documentation*

---

## G

### GFM (GitHub Flavored Markdown)
An extended Markdown flavor used by GitHub. Adds features beyond CommonMark: tables, strikethrough, task lists, emoji, autolinks, and more. Spec: github.github.com/gfm. *See also: CommonMark, Markdown Flavors*

### Git
The version control system used in Docs as Code workflows. Markdown files are stored in Git repositories, enabling branching, review, versioning, and CI/CD. *See also: Docs as Code, GitFlow, GitHub*

### GitBook
A documentation platform (now GitBook.com) that uses Markdown files and provides hosting, collaboration, and publishing. Originally an open-source tool, now a SaaS platform. *See also: Documentation Site, Docusaurus, MkDocs*

### GitHub Actions
CI/CD service integrated with GitHub. Used for automated documentation testing, building, previewing, and deploying. *See also: CI/CD, Docs as Code*

### GitHub Pages
Free static site hosting from GitHub. Commonly used to host documentation sites generated from Markdown files. Works with Jekyll natively or any SSG. *See also: Jekyll, Static Site Generator*

### GitLab CI
GitLab's built-in CI/CD service. Can be configured to build, test, and deploy documentation sites. *See also: CI/CD, GitHub Actions*

### Grammar Checker
A tool that checks documentation for grammatical errors. LanguageTool is a common choice. Often integrated into CI/CD pipelines. *See also: Prose Linter, Vale, LanguageTool*

### Graphviz
An open-source graph visualization software. Can generate diagrams from DOT language descriptions. Sometimes used alongside or instead of Mermaid for complex diagrams. *See also: Mermaid, DOT Language*

### Gruber, John
The co-creator of Markdown (with Aaron Swartz) in 2004. Maintained the original Markdown.pl implementation. *See also: History of Markdown, Swartz, Aaron*

---

## H

### Hard Break
A line break in Markdown created by ending a line with two or more spaces and a newline. Renders as a `<br>` in HTML. *See also: Line Break, Soft Break*

### Hashtag
A tag or metadata label, originally from social media. In Markdown note-taking tools (Obsidian, Logseq), `#tag` creates searchable tags. *See also: Obsidian, Tag*

### Heading
A section title in Markdown, created with 1-6 `#` characters (`# H1` through `###### H6`). Headings should form a logical hierarchy for accessibility and SEO. *See also: TOC, Accessibility*

### Highlight
Text that is highlighted (typically yellow background). Some flavors support `==highlighted==` syntax. In standard Markdown, use `<mark>highlighted</mark>` HTML. *See also: HTML in Markdown*

### History of Markdown
Markdown was created in 2004 by John Gruber and Aaron Swartz, inspired by conventions in plain text email. Version 1.0 was released in 2004. The lack of a formal spec led to fragmentation, which CommonMark later addressed. *See also: CommonMark, Gruber John, Swartz Aaron*

### Horizontal Rule (HR)
A thematic break or horizontal line created with `---`, `***`, or `___`. Renders as `<hr>` in HTML. *See also: Block Element*

### HTML in Markdown
Raw HTML can be embedded in Markdown for elements not supported by native syntax. Common uses: `<div>` for layout, `<video>` for videos, `<details>` for collapsible sections, `<kbd>` for keyboard shortcuts. *See also: Raw HTML*

### Hugo
A static site generator written in Go. Known for exceptional build speed. Supports Markdown content with front matter, shortcodes, templates, and multilingual mode. *See also: Docusaurus, Jekyll, MkDocs, Static Site Generator*

### Hyperlink
A reference to another document or resource. In Markdown, created with `[text](url)` syntax. *See also: Cross-Reference, Internal Link, Reference-Style Link*

---

## I

### i18n (Internationalization)
The process of designing documentation to support multiple languages. SSGs like Docusaurus and Hugo have built-in i18n support. Often involves separate Markdown files per locale. *See also: Docusaurus, Hugo, Locale*

### IDE (Integrated Development Environment)
A software application for writing code. Popular IDEs for Markdown: VS Code (with extensions), IntelliJ IDEA, Vim, Emacs. *See also: Editor, VS Code*

### Image
A visual element embedded in Markdown with `![alt text](url)` syntax. Supports optional title text: `![alt](url "title")`. *See also: Alt Text, Figure, SVG*

### Inline Element
A Markdown element that appears within a line of text, such as bold, italic, code spans, links, and images. *See also: Block Element, Code Span*

### Inline Math
LaTeX math notation embedded within a line of text, delimited by `$...$`. Used for equations and mathematical expressions. *See also: LaTeX, Math Block, MathJax, KaTeX*

### Internal Link
A link to another section within the same document or documentation site. Created with `[text](#section-id)` for anchors or `[text](../file.md)` for relative files. *See also: Anchor, Cross-Reference, Relative Link*

### Italic
Text rendered in italic using `*single asterisks*` or `_single underscores_`. *See also: Bold, Emphasis, Strong*

---

## J

### Jekyll
A static site generator built in Ruby, tightly integrated with GitHub Pages. Uses Markdown with Liquid templates. Popular for blogs and documentation. Was the default for GitHub Pages. *See also: GitHub Pages, Hugo, Static Site Generator*

### JSON (JavaScript Object Notation)
A lightweight data interchange format. Used in Markdown for front matter (`---json`), configuration files, and data tables. *See also: Front Matter, TOML, YAML*

### JSON Schema
A vocabulary for annotating and validating JSON documents. Used to validate front matter structures in documentation projects. *See also: Front Matter, JSON, Validation*

---

## K

### KaTeX
A fast, lightweight JavaScript library for rendering LaTeX math on the web. Commonly used with Markdown in static site generators. Faster than MathJax but supports fewer LaTeX features. *See also: LaTeX, Math Block, MathJax*

### kbd (Keyboard)
An HTML element `<kbd>` used in Markdown to denote keyboard input or shortcuts. Often combined with `+` for chord notation: `<kbd>Ctrl</kbd>+<kbd>C</kbd>`. *See also: HTML in Markdown*

### Keep a Changelog
A convention for maintaining a CHANGELOG.md file with structured Markdown. Uses semantic versioning, ISO dates, and categorized changes (Added, Changed, Deprecated, Removed, Fixed, Security). *See also: Changelog, Semantic Versioning*

### Knowledge Base
A centralized repository of information, often built with Markdown and a static site generator. Used for internal documentation, FAQs, and reference materials. *See also: Digital Garden, Documentation Site, Wiki*

### Knowledge Management
The process of creating, sharing, using, and managing knowledge. Markdown is a core tool for knowledge management systems due to its portability and simplicity. *See also: Digital Garden, Knowledge Base, Obsidian*

---

## L

### LanguageTool
An open-source grammar, style, and spell checker. Can be integrated into CI/CD pipelines for automated documentation proofreading. Supports 30+ languages. *See also: Grammar Checker, Prose Linter, Vale*

### LaTeX
A typesetting system commonly used for mathematical and scientific documents. LaTeX math can be embedded in Markdown using `$...$` (inline) or `$$...$$` (block) delimiters with MathJax or KaTeX rendering. *See also: KaTeX, Math Block, MathJax*

### Line Break
A newline in the output. In Markdown, a regular newline creates a space in the output; a hard break requires two trailing spaces. `<br>` can also be used. *See also: Hard Break, Soft Break*

### Link Checker
A tool that verifies all links in documentation are valid. Common tools: lychee, broken-link-checker, htmlproofer. Often run in CI/CD pipelines. *See also: CI/CD, Documentation Testing*

### Liquid
A template language created by Shopify, used by Jekyll. Allows variables, loops, conditionals, and filters in Markdown files processed by Jekyll. *See also: Jekyll, Shortcode, Template*

### List
A collection of items in Markdown. Types: ordered (1. 2. 3.), unordered (- * +), nested (indented), task ([ ] [x]), and definition (Term: Definition). *See also: Definition List, Task List*

### Locale
A specific language and region combination (e.g., `en-US`, `fr-FR`, `zh-CN`). Used in i18n documentation to organize translations. *See also: i18n*

### Lorum Ipsum
Placeholder text commonly used in documentation mockups. While not Markdown-specific, often appears in Markdown-based prototypes and templates. *See also: Template*

### Lychee
A fast, async link checker written in Rust. Commonly used in CI/CD pipelines to verify Markdown documentation links. *See also: CI/CD, Link Checker*

---

## M

### Markdown
A lightweight markup language created by John Gruber and Aaron Swartz in 2004. Designed to be easy to read and write in plain text, while converting to valid HTML. The `.md` extension is standard. *See also: CommonMark, GFM, Markdown Flavors*

### Markdown Flavors
Different implementations and extensions of Markdown. Major flavors: CommonMark (standardized), GFM (GitHub), MultiMarkdown (extended), RMarkdown (R stats), MDX (React), Markdown Extra (PHP), Pandoc Markdown (universal). *See also: CommonMark, GFM, MDX, MultiMarkdown, RMarkdown*

### markdown-it
A popular, extensible Markdown parser for JavaScript. Plugin architecture supports custom syntax extensions. Used by many tools and editors. *See also: marked, Parser, remark*

### Markdown Lint
A tool (with many implementations: markdownlint, remark-lint, markdownlint-cli) that checks Markdown files for style and syntax errors. Enforces rules like MD001 (heading increment), MD013 (line length), MD033 (inline HTML), and 50+ others. *See also: Linter, markdownlint*

### markdownlint
The most popular Markdown linting tool, available as a CLI (`markdownlint-cli`), VS Code extension, and GitHub Action. Enforces ~60+ configurable rules (MD001-MD058). *See also: Linter, Markdown Lint*

### Markmap
A tool that converts Markdown headings into interactive mind maps. Useful for visualizing document structure and information hierarchy. *See also: Mind Map, Mermaid*

### Marp
A Markdown presentation tool that converts `.md` files into slide decks. Supports themes, custom CSS, code highlighting, and multiple output formats (HTML, PDF, PPTX). *See also: Presentation, Slidev*

### Math Block
A block-level LaTeX math expression, delimited by `$$...$$` or `\[...\]`. Rendered as a centered, standalone equation. *See also: Inline Math, KaTeX, LaTeX, MathJax*

### MathJax
A JavaScript library that renders LaTeX math in browsers. Supports TeX, LaTeX, MathML, and AsciiMath notation. Heavier than KaTeX but supports more LaTeX features. *See also: KaTeX, LaTeX, Math Block*

### MDX
An extension to Markdown that allows JSX (JavaScript XML) within Markdown files. Enables importing and using React components directly in documentation. Used by Docusaurus, Next.js, and Storybook. *See also: Docusaurus, React, JSX*

### Mermaid
A JavaScript-based diagramming and charting tool that renders Markdown-inspired text definitions to diagrams. Supports flowcharts, sequence diagrams, class diagrams, state diagrams, ERDs, Gantt charts, pie charts, quadrant charts, mindmaps, timeline diagrams, and more. *See also: Diagram, Flowchart, Gantt Chart, Sequence Diagram*

### Metadata
Data about data. In Markdown, metadata is stored in front matter (YAML, TOML, JSON) and includes title, date, tags, categories, author, description, and custom fields. *See also: Front Matter, Taxonomy*

### Mind Map
A diagram used to visually organize information, typically with a central concept and branching subtopics. Mermaid supports mind maps with the `mindmap` keyword. *See also: Markmap, Mermaid*

### MkDocs
A static site generator for project documentation, built in Python. Known for its simplicity and the popular Material for MkDocs theme. Uses Markdown with extensions and plugins. *See also: Docusaurus, ReadTheDocs, Static Site Generator*

### MkDocs Material
A feature-rich theme for MkDocs, created by Martin Donath and contributors. Adds navigation, search (with term highlighting), tabs, annotations, social cards, versioning, and blog support. The most popular MkDocs theme. *See also: MkDocs, Theme*

### Monospace Font
A font where each character occupies the same width. Used in code blocks, code spans, and terminal output. Common monospace fonts: Fira Code, JetBrains Mono, Source Code Pro, Cascadia Code, Consolas, Monaco. *See also: Code Block, Code Span*

### MultiMarkdown
An extended Markdown flavor by Fletcher Penney. Adds footnotes, tables, citations, math, metadata, cross-references, glossary, and multiple output formats. Supports file transclusion. *See also: Markdown Flavors, Pandoc*

---

## N

### Next.js
A React framework that supports Markdown and MDX content through its `@next/mdx` integration. Used for building documentation sites, blogs, and marketing pages from Markdown files. *See also: MDX, React*

### Note
A callout type used to highlight additional information. Written as `> [!NOTE]` in GitHub Flavored Markdown and Obsidian. *See also: Callout, Blockquote*

### Nunjucks
A templating engine for JavaScript, similar to Jinja2. Used by some static site generators to extend Markdown with dynamic content, includes, and macros. *See also: Template, Liquid, Jinja2*

---

## O

### Obsidian
A popular note-taking and knowledge management application. Stores notes as plain Markdown files in a local folder. Supports backlinks, graph view, plugins, themes, and custom CSS. Key for building digital gardens and personal knowledge bases. *See also: Digital Garden, Knowledge Base, Markdown Editor*

### OpenAPI
A specification for describing RESTful APIs. OpenAPI documents (written in YAML or JSON) can be used to generate interactive API documentation rendered with Markdown. Formerly known as Swagger. *See also: API Documentation, Swagger*

### Open Source Documentation
Documentation for open-source projects. Typically written in Markdown, stored in the project repository, and includes README, CONTRIBUTING, CODE_OF_CONDUCT, CHANGELOG, and LICENSE files. *See also: README, CONTRIBUTING, CHANGELOG*

### Ordered List
A numbered list created with `1.`, `2.`, etc. Automatic numbering works if all items use `1.`. Nested ordered lists use indentation. *See also: List, Task List, Unordered List*

### Org-mode (Org)
A plain-text markup format and major mode for Emacs. Similar to Markdown but more powerful for task management, agendas, tables, and literate programming. Can be exported to Markdown. *See also: Markdown Comparison*

---

## P

### Pandoc
The "swiss-army knife" of document conversion. Converts between Markdown and dozens of other formats: HTML, PDF, DOCX, LaTeX, EPUB, reStructuredText, AsciiDoc, and more. Supports custom templates and filters. *See also: Document Conversion, Markdown to PDF*

### Parser
A software component that reads Markdown text and converts it into a structured format (like an AST). Examples: marked (JS), markdown-it (JS), mistune (Python), pulldown-cmark (Rust), cmark (C). *See also: AST, Renderer, Tokenization*

### Permalink
A permanent URL to a specific heading or section of a documentation page. SSGs automatically generate permalinks from headings. Example: `docs/page/#installation`. *See also: Anchor, Cross-Reference, URL Slug*

### Plugin
An extension that adds functionality to a Markdown processor or SSG. Examples: remark plugins, markdown-it plugins, MkDocs plugins, Docusaurus plugins. *See also: Extension, remark*

### Prettier
An opinionated code formatter that supports Markdown. Can auto-format Markdown files for consistent spacing, wrapping, and table formatting. *See also: dprint, Formatter, markdownlint*

### Preview
A rendered view of Markdown content, typically shown alongside the editor (split view) or in a separate tab. Most editors offer live preview that updates as you type. *See also: Editor, VS Code, WYSIWYG*

### Prose Linter
A tool that checks documentation for style, tone, and readability issues. Popular options: Vale, Alex, write-good, proselint. *See also: Grammar Checker, Vale, Linter*

### Published
A document marked as ready for public consumption. In SSGs, controlled via front matter: `published: true`. The opposite of `draft`. *See also: Draft, Front Matter*

### Python-Markdown
A Python implementation of Markdown with an extension mechanism. Supports 20+ built-in extensions (tables, footnotes, code hilite, toc, attr_list, admonition, md_in_html). *See also: Parser, Extension, mistune*

---

## Q

### Quarto
An open-source scientific and technical publishing system. Uses Markdown with extensions for executable code blocks (R, Python, Julia, Observable), cross-references, citations, and multiple output formats. Successor to RMarkdown in many ways. *See also: Knitr, RMarkdown*

### Quick Start
A section in documentation that helps users get started quickly. Typically includes installation, minimal configuration, and a simple example. Often the first section after the introduction. *See also: Documentation, Tutorial*

---

## R

### Raw HTML
HTML code embedded directly in Markdown files. Used for elements not covered by Markdown syntax: `<div>`, `<span>`, `<video>`, `<audio>`, `<details>`, `<summary>`, `<kbd>`, custom attributes, and inline styles. *See also: HTML in Markdown*

### React
A JavaScript library for building user interfaces. Relevant to Markdown through MDX, which allows React components within Markdown files. Used by Docusaurus, Storybook, and Next.js documentation. *See also: Docusaurus, MDX, Next.js*

### ReadTheDocs (RTD)
A documentation hosting platform. Automatically builds and deploys documentation from Git repositories. Supports MkDocs, Sphinx (reStructuredText), and custom SSGs. *See also: Docusaurus, MkDocs, Sphinx*

### README
The primary entry-point document for a project, automatically displayed by Git hosting services. Contains project description, installation, usage, and contribution information. Written in Markdown (`.md`). *See also: CONTRIBUTING, Documentation, Open Source Documentation*

### Reference-Style Link
A link defined with two parts: an inline reference `[text][label]` and a definition `[label]: url "title"`. Useful for readability and reusing links. *See also: Hyperlink, Inline Link*

### Relative Link
A link specified relative to the current document's location. Essential for documentation sites with multiple pages. Example: `[Installation](../installation.md)`. *See also: Hyperlink, Internal Link*

### remark
A Markdown processor built on a unified.js ecosystem. Uses AST (mdast) for transformation. Supports 100+ plugins for linting, formatting, syntax extensions, and output generation. *See also: AST, markdown-it, Plugin, unified*

### Renderer
A component that converts the internal representation (AST) of Markdown into output format (HTML, PDF, etc.). Can be customized to produce different output formats. *See also: AST, Parser, Tokenization*

### reStructuredText (reST, RST)
A markup language used primarily with the Sphinx documentation system. More feature-rich than Markdown but has a stricter syntax. Python community's preferred documentation format. *See also: DocUtils, Markdown Comparison, Sphinx*

### RMarkdown
An extension of Markdown for R and data science. Supports embedded R code chunks, inline R expressions, and multiple output formats (HTML, PDF, Word, slides). Created by RStudio. *See also: Bookdown, Knitr, Quarto*

---

## S

### Semantic Versioning (SemVer)
A versioning scheme: `MAJOR.MINOR.PATCH` (e.g., 2.1.3). Used in changelogs and documentation versioning to communicate the nature of changes. *See also: Changelog, Keep a Changelog*

### SEO (Search Engine Optimization)
The practice of optimizing documentation to rank higher in search results. Involves proper heading hierarchy, meta tags, descriptive URLs, alt text, and structured data. *See also: Accessibility, Front Matter, Metadata*

### Sequence Diagram
A diagram that shows how processes or objects interact over time. Created in Mermaid using the `sequenceDiagram` keyword. *See also: Diagram, Mermaid*

### Shields.io
A service for creating metadata badges for README files. Badges display build status, version, license, coverage, downloads, and more. Embedded using Markdown image syntax. *See also: Badge, README*

### Shortcode
A reusable snippet of code in static site generators that generates complex output. Hugo is known for its shortcode system. Example: `{{< youtube id >}}`. *See also: Hugo, Template, Liquid*

### Sidebar
A navigation panel in documentation sites. Configured via front matter, YAML files, or theme configuration. Shows the document hierarchy for easy navigation. *See also: Documentation Site, Navigation, TOC*

### Slidev
A presentation tool for developers that converts Markdown files into interactive slides. Supports code highlighting, LaTeX, diagrams, themes, and recording. *See also: Marp, Presentation*

### Slug
A URL-friendly version of a title. Example: "Getting Started" becomes "getting-started". Generated automatically from headings by SSGs. *See also: Anchor, Permalink*

### Soft Break
A line break within the same paragraph. In Markdown, just continue typing on the next line (most implementations treat adjacent lines as the same paragraph). *See also: Hard Break, Line Break*

### Spell Checker
A tool that checks documentation for spelling errors. Common tools: cspell, hunspell, codespell. Can be integrated into editors, CI/CD, and documentation quality pipelines. *See also: CI/CD, Linter, Prose Linter*

### Sphinx
A documentation generator built for Python that uses reStructuredText. Widely used in the Python ecosystem. Can be configured to use Markdown via MyST (Markedly Structured Text) parser. *See also: ReadTheDocs, reStructuredText, MyST*

### State Diagram
A diagram that shows the states of a system and transitions between them. Created in Mermaid using the `stateDiagram-v2` keyword. *See also: Diagram, Mermaid*

### Static Site Generator (SSG)
A tool that generates a complete HTML website from source files (usually Markdown). Common SSGs: Docusaurus, Hugo, Jekyll, MkDocs, VitePress, Next.js, Eleventy, Gatsby. *See also: Docusaurus, Hugo, Jekyll, MkDocs*

### Strikethrough
Text with a horizontal line through it, created with `~~text~~`. Supported in GFM and many other flavors. *See also: GFM, Text Formatting*

### Strong
A strong emphasis, rendered as bold. Created with `**double asterisks**`. *See also: Bold, Emphasis, Italic*

### Subscript
Text rendered smaller and below the baseline, created with `<sub>text</sub>`. Useful for chemical formulas (H₂O). *See also: Superscript, HTML in Markdown*

### Superscript
Text rendered smaller and above the baseline, created with `<sup>text</sup>`. Useful for footnotes, ordinals (1ˢᵗ, 2ⁿᵈ), and exponents. *See also: HTML in Markdown, Subscript*

### SVG (Scalable Vector Graphics)
An XML-based vector image format. Can be embedded inline in Markdown. Supports scaling without quality loss. Ideal for diagrams, logos, and illustrations in documentation. *See also: Image, Mermaid*

### Swagger
Former name for the OpenAPI Specification. Swagger tools (Swagger UI, Swagger Editor) generate interactive API documentation from OpenAPI specs. *See also: API Documentation, OpenAPI*

### Swartz, Aaron
Co-creator of Markdown with John Gruber in 2004. Also a key contributor to RSS, Creative Commons, and Reddit. *See also: Gruber John, History of Markdown*

### Syntax Highlighting
Color-coded display of code that improves readability. In Markdown, activated by specifying a language after the opening code fence: ` ```python `. Supported for 100+ languages. *See also: Code Block, Fenced Code Block*

---

## T

### Table
A structured data grid in Markdown, created with pipes `|` and dashes `-`. Supports alignment with colons. Example:
```
| Left | Center | Right |
|:-----|:------:|------:|
| a    |   b    |     c |
```
*See also: GFM, Table Alignment*

### Table of Contents (TOC)
A list of headings that serves as navigation within a document. Created manually, via `[TOC]` in some flavors, automatically by SSGs, or using JavaScript-based TOC generators. *See also: Heading, Navigation, Sidebar*

### Tag
A keyword or label assigned to a document for categorization. Stored in front matter: `tags: [markdown, documentation]`. Used for filtering, grouping, and navigation. *See also: Category, Front Matter, Taxonomy*

### Task List
A checklist created with `- [ ]` (unchecked) and `- [x]` (checked) syntax. Supported by GFM and many other platforms. *See also: GFM, List*

### Taxonomy
A system of classification. In documentation, taxonomies (tags, categories, series) are defined in front matter and used for content organization and navigation. *See also: Category, Front Matter, Tag*

### Template
A reusable file pattern that controls how Markdown content is rendered. Used by SSGs to wrap content in consistent layouts (headers, footers, sidebars, etc.). *See also: Liquid, Nunjucks, Shortcode*

### Theme
A visual design system for documentation sites. Controls colors, fonts, spacing, layout, and component styling. MkDocs Material, Docusaurus Classic, Hugo Book are popular themes. *See also: Docusaurus, Hugo, MkDocs Material*

### Tokenization
The first phase of Markdown parsing, where raw text is split into tokens (headings, paragraphs, code blocks, etc.). Tokens are then structured into an AST. *See also: AST, Parser, Renderer*

### TOML (Tom's Obvious Minimal Language)
A configuration file format similar to INI but with more structure. Used for front matter in some tools like Hugo and Cargo (Rust). *See also: Front Matter, JSON, YAML*

### Transclusion
The inclusion of content from one file into another. Some Markdown processors support `{{ file.md }}` or custom include directives. Not part of standard Markdown. *See also: Include, MultiMarkdown*

### Tutorial
A step-by-step guide that teaches a user how to accomplish a specific task. A key component of technical documentation alongside guides, reference, and explanation. *See also: Documentation, Quick Start*

### Typora
A minimalist Markdown editor that provides a seamless live preview (WYSIWYG-like experience). Supports themes, math, diagrams, code highlighting, and file management. *See also: Editor, Obsidian, VS Code*

---

## U

### unified
A JavaScript ecosystem of tools for processing text documents. The foundation for remark (Markdown), rehype (HTML), and retext (natural language). Uses AST-based processing pipelines with plugins. *See also: AST, mdast, remark, rehype*

### Unordered List
A bulleted list created with `-`, `*`, or `+`. Supports nesting via indentation. *See also: List, Ordered List, Task List*

### URL Slug
*See: Slug*

---

## V

### Vale
An open-source, command-line prose linter. Supports custom style rules, multiple formats (Markdown, HTML, reStructuredText), and integration with editors and CI/CD. *See also: Grammar Checker, Prose Linter*

### Validation
The process of checking Markdown documents for correctness. Includes link validation, front matter validation, HTML validation, and compliance with linting rules. *See also: Linter, markdownlint, Link Checker*

### Vercel
A cloud platform for static sites and serverless functions. Commonly used to deploy documentation sites built with Docusaurus, Next.js, or Hugo. *See also: Netlify, Deployment*

### Versioning
The practice of maintaining multiple versions of documentation to match software versions. Docusaurus has built-in versioning. MkDocs supports it via plugins. *See also: Docusaurus, Semantic Versioning*

### VS Code (Visual Studio Code)
A popular code editor with excellent Markdown support. Extensions: markdownlint, Markdown Preview Enhanced, Prettier, GitHub Markdown Preview, Paste Image, Mermaid Preview, yaml. *See also: Editor, IDE*

---

## W

### Wiki
A collaborative website that can be edited by users. Many wikis (like Wikipedia) use Markdown-like syntax. Wiki pages often link to each other automatically via WikiWords. *See also: Digital Garden, Knowledge Base, Wiki Link*

### Wiki Link
A link to another page using `[[double bracket]]` notation, popularized by wikis and used by Obsidian, Roam Research, and Logseq for internal linking. *See also: Obsidian, Wiki*

### WYSIWYG (What You See Is What You Get)
An editing interface where content appears as it will in the final output. Typora provides a WYSIWYG-like experience for Markdown. Most Markdown editors use a split-pane (source + preview) approach. *See also: Editor, Preview, Typora*

---

## Y

### YAML (YAML Ain't Markup Language)
A human-readable data serialization language. Used extensively in Markdown for front matter, configuration files, and data definitions. Indentation-sensitive. Supports strings, numbers, booleans, arrays, objects, and nested structures. *See also: Front Matter, JSON, TOML*

### YAML Front Matter
The most common format for front matter in Markdown files. Delimited by `---` lines. Example:
```yaml
---
title: "My Document"
date: 2026-01-01
tags: [markdown, documentation]
---
```
*See also: Front Matter, YAML, JSON Front Matter, TOML Front Matter*

---

## Z

### Zettelkasten
A note-taking methodology where each note is a single idea, densely linked to other notes. Obsidian and other Markdown-based tools are popular for implementing Zettelkasten systems. *See also: Digital Garden, Knowledge Base, Obsidian*

### Zola
A static site generator written in Rust. Uses Markdown with TOML front matter. Known for its speed and simplicity. Competes with Hugo in the performance-focused SSG space. *See also: Hugo, Static Site Generator*

---

<p align="center">
  <strong>Master the vocabulary of documentation engineering.</strong>
</p>
