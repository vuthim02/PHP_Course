# Module 8: HTML Inside Markdown

## 8.1 HTML Support in Markdown

### Overview

Markdown was designed by John Gruber in 2004 with the explicit goal of being a lightweight markup language that can contain inline HTML. This design decision was deliberate: Markdown handles the simple formatting tasks (headings, lists, emphasis, links, images) while HTML handles everything else — from complex tables to multimedia embeds to custom layouts.

### How HTML Integration Works

Markdown parsers process HTML according to a specific set of rules defined in the CommonMark specification:

1. **Inline HTML** — HTML tags that do not create block-level elements are passed through the Markdown parser and rendered as HTML alongside the Markdown-generated HTML.

2. **Block-level HTML** — HTML tags at the beginning of a line that represent block-level elements cause the parser to switch into "HTML mode," where everything until the closing tag is treated as raw HTML, not Markdown.

3. **Raw HTML passing** — The Markdown parser recognizes HTML tags and passes them through without processing. This means you can use any valid HTML inside Markdown, and it will be rendered as HTML.

### CommonMark Rules

The CommonMark specification (the standardized version of Markdown) defines precise rules:

- **Block HTML** is any HTML tag that is a block-level element (`<div>`, `<table>`, `<pre>`, `<p>`, `<ul>`, `<ol>`, `<blockquote>`, `<details>`, `<section>`, `<article>`, `<header>`, `<footer>`, `<nav>`, `<aside>`, `<main>`, `<figure>`, `<figcaption>`, `<form>`, `<fieldset>`, `<textarea>`, `<canvas>`, `<video>`, `<audio>`).

- **Inline HTML** includes all other HTML tags (`<span>`, `<a>`, `<img>`, `<br>`, `<em>`, `<strong>`, `<code>`, `<kbd>`, `<sup>`, `<sub>`, `<abbr>`, `<cite>`, `<dfn>`, `<mark>`, `<del>`, `<ins>`, `<small>`, `<b>`, `<i>`, `<u>`, `<s>`).

- **Blank lines** between block HTML and Markdown content are significant — they determine whether the Markdown following the HTML is parsed or treated as raw text.

### A Simple Example

```markdown
This is Markdown with <span style="color: red;">red text</span> inline.

<div class="warning">
This is inside a div. Markdown here is NOT processed.

</div>

<div markdown="1">
This is inside a div with markdown=1. **Markdown IS processed** in some parsers.
</div>
```

### When to Use HTML vs Markdown

| Task | Use Markdown | Use HTML |
|------|-------------|----------|
| Headings | `# Heading` | `<h1>Heading</h1>` |
| Bold/Italic | `**bold** *italic*` | `<strong>` / `<em>` |
| Links | `[text](url)` | `<a href="url">` |
| Images | `![alt](src)` | `<img src="" alt="">` |
| Lists | `- item` | `<ul><li>` |
| Tables | Not native | `<table>` |
| Multimedia | Not native | `<video>` / `<audio>` |
| Custom styling | Not supported | `<span style="">` |
| Layout | Not supported | `<div>` / CSS Grid |
| Interactive | Not supported | `<details>` / forms |
| Line breaks | Two spaces | `<br>` |

---

## 8.2 Inline HTML

### Overview

Inline HTML tags can be used within Markdown paragraphs, headings, and other block-level elements. They are rendered as HTML and can include attributes like `style`, `class`, `id`, `data-*`, and more.

### `<span>` — Inline Container

The most versatile inline HTML tag, used for applying styles, classes, or JavaScript hooks to text:

```html
<p>This is <span style="color: #e74c3c; font-weight: bold;">important</span> text.</p>
```

Usage in Markdown:

```markdown
The system reported a <span style="color: red;">critical error</span> during startup.

Use <span class="highlight">semantic versioning</span> for your packages.
```

### `<abbr>` — Abbreviations

Defines an abbreviation or acronym, with the full expansion shown as a tooltip:

```html
<abbr title="HyperText Markup Language">HTML</abbr>
<abbr title="Cascading Style Sheets">CSS</abbr>
<abbr title="Application Programming Interface">API</abbr>
<abbr title="Asynchronous JavaScript And XML">AJAX</abbr>
```

In Markdown:

```markdown
The <abbr title="Document Object Model">DOM</abbr> is a programming interface for web documents.

We use <abbr title="JavaScript Object Notation">JSON</abbr> for data exchange.
```

### `<kbd>` — Keyboard Input

Represents keyboard input, typically rendered in a monospace font with a key-like appearance:

```html
Press <kbd>Ctrl</kbd> + <kbd>C</kbd> to copy.
Press <kbd>Cmd</kbd> + <kbd>Shift</kbd> + <kbd>P</kbd> to open command palette.
Press <kbd>F5</kbd> to refresh the page.
<kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Del</kbd>
```

In Markdown:

```markdown
To save your work, press <kbd>Ctrl</kbd> + <kbd>S</kbd>.

Exit vi by typing <kbd>:</kbd> <kbd>q</kbd> <kbd>!</kbd> and pressing <kbd>Enter</kbd>.
```

### `<sup>` — Superscript

Raises text above the baseline, commonly used for footnotes, exponents, and ordinal indicators:

```html
E = mc<sup>2</sup>
The 1<sup>st</sup> of January
Footnote reference<sup><a href="#fn1">[1]</a></sup>
x<sup>2</sup> + y<sup>2</sup> = r<sup>2</sup>
```

In Markdown:

```markdown
The 21<sup>st</sup> century began in 2001.

Water is H<sup>+</sup> + OH<sup>-</sup>.
```

### `<sub>` — Subscript

Lowers text below the baseline, used for chemical formulas and mathematical indices:

```html
H<sub>2</sub>O
CO<sub>2</sub>
C<sub>6</sub>H<sub>12</sub>O<sub>6</sub>
x<sub>1</sub>, x<sub>2</sub>, ..., x<sub>n</sub>
```

In Markdown:

```markdown
The chemical formula for glucose is C<sub>6</sub>H<sub>12</sub>O<sub>6</sub>.

The sequence a<sub>1</sub>, a<sub>2</sub>, ..., a<sub>n</sub> converges.
```

### `<mark>` — Highlighted Text

Highlights text for reference or notation purposes, typically rendered with a yellow background:

```html
This is <mark>highlighted</mark> text.
The <mark>key finding</mark> of the study was unexpected.
Please review the <mark style="background: #ff6b6b;">critical section</mark>.
```

In Markdown:

```markdown
The <mark>most important</mark> step is validating the input.

<mark class="warning">This configuration is deprecated in v3.0.</mark>
```

### `<del>` — Deleted Text

Represents text that has been deleted or removed, typically rendered with a strikethrough:

```html
The price was <del>$49.99</del> now $29.99!
<del>This feature will be removed in the next version.</del>
```

In Markdown:

```markdown
The old API endpoint <del><code>/api/v1/users</code></del> is deprecated.

Our Q1 target was <del>1000 users</del>, but we actually reached 2500!
```

### `<ins>` — Inserted Text

Represents text that has been inserted, typically rendered with an underline:

```html
The new <ins>feature</ins> is now available.
Please <ins>add your email address</ins> to the form.
```

In Markdown:

```markdown
The <ins>new implementation</ins> replaces the legacy system.

Changes: <del>Old behavior</del> <ins>New behavior</ins>
```

### `<cite>` — Citations

Represents a reference to a creative work, typically rendered in italics:

```html
<cite>The Pragmatic Programmer</cite> by Andrew Hunt and David Thomas
<cite>Clean Code</cite> by Robert C. Martin
As stated in <cite>Designing Data-Intensive Applications</cite>
```

### `<dfn>` — Definition

Represents the defining instance of a term, typically rendered in italics:

```html
<dfn>Markdown</dfn> is a lightweight markup language.
<dfn>API</dfn> stands for Application Programming Interface.
<dfn id="term-cache">Cache</dfn> is a hardware or software component that stores data.
```

### Combined Example

```markdown
## HTML Inline Tags in Action

The <abbr title="HyperText Preprocessor">PHP</abbr> function returns a
<dfn>P<sup>DO</sup></dfn> (<mark>PHP Data Object</mark>).

<kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>I</kbd> opens Developer Tools.

The formula for water is H<sub>2</sub>O and E=mc<sup>2</sup>.

Price: <del>$99</del> <ins>$49</ins> — <mark>50% off!</mark>

As <cite>The Art of Unix Programming</cite> states, <q>Rule of Simplicity</q>.
```

---

## 8.3 Block-Level HTML

### Overview

Block-level HTML elements create distinct blocks of content. In Markdown, when a block-level HTML tag appears at the beginning of a line, the Markdown parser switches to HTML mode and treats everything until the closing tag as raw HTML. This means Markdown syntax inside block-level HTML is NOT processed (unless the parser supports the `markdown=1` attribute).

### `<div>` — Content Division

The most common block-level container, used for grouping content and applying styles:

```html
<div class="alert alert-warning">
    <strong>Warning!</strong> This configuration is deprecated.
</div>

<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #007bff;">
    <h3>Tip</h3>
    <p>Always validate user input on both client and server sides.</p>
</div>
```

### `<section>` — Thematic Grouping

Represents a standalone section of content, typically with a heading:

```html
<section class="installation">
    <h2>Installation</h2>
    <p>Follow these steps to install the package...</p>
</section>

<section class="configuration">
    <h2>Configuration</h2>
    <p>Configure the application settings...</p>
</section>
```

### `<article>` — Self-Contained Content

Represents a self-contained composition that could be independently distributed:

```html
<article class="blog-post">
    <header>
        <h1>Getting Started with Docker</h1>
        <p class="meta">Published on <time datetime="2025-01-15">January 15, 2025</time></p>
    </header>
    <p>Docker is a platform for developing, shipping, and running applications...</p>
    <footer>
        <p>Tags: <a href="/tags/docker">docker</a>, <a href="/tags/devops">devops</a></p>
    </footer>
</article>
```

### `<aside>` — Side Content

Represents content tangentially related to the main content:

```html
<aside class="sidebar">
    <h3>Quick Links</h3>
    <ul>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#configuration">Configuration</a></li>
        <li><a href="#usage">Usage</a></li>
    </ul>
</aside>
```

```html
<aside class="note">
    <p><strong>Note:</strong> The API rate limit is 1000 requests per hour.</p>
</aside>
```

### `<header>` — Introductory Content

Contains introductory or navigational content:

```html
<header class="page-header">
    <h1>Documentation</h1>
    <nav>
        <a href="/">Home</a> /
        <a href="/docs">Docs</a> /
        <span>Current Page</span>
    </nav>
</header>
```

### `<footer>` — Footer Content

Contains footer information for a section or page:

```html
<footer class="article-footer">
    <p>Last updated: <time datetime="2025-03-10">March 10, 2025</time></p>
    <p>Found a mistake? <a href="https://github.com/example/docs/issues">File an issue</a>.</p>
</footer>
```

### `<main>` — Main Content

Represents the dominant content of the document:

```html
<main>
    <h1>User Guide</h1>
    <p>Welcome to the user guide. This document covers...</p>

    <section>
        <h2>Getting Started</h2>
        <p>Begin by creating an account...</p>
    </section>
</main>
```

### `<nav>` — Navigation

Represents a section with navigation links:

```html
<nav class="breadcrumbs">
    <a href="/">Home</a> &raquo;
    <a href="/docs">Documentation</a> &raquo;
    <span>API Reference</span>
</nav>

<nav class="toc">
    <h3>Table of Contents</h3>
    <ol>
        <li><a href="#introduction">Introduction</a></li>
        <li><a href="#installation">Installation</a></li>
        <li><a href="#configuration">Configuration</a></li>
    </ol>
</nav>
```

### Practical Layout Example

```html
<div class="documentation-layout" style="display: grid; grid-template-columns: 250px 1fr; gap: 30px; max-width: 1200px; margin: 0 auto;">

    <nav class="sidebar" style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h3>Contents</h3>
        <ul>
            <li><a href="#intro">Introduction</a></li>
            <li><a href="#setup">Setup</a></li>
            <li><a href="#usage">Usage</a></li>
            <li><a href="#api">API Reference</a></li>
        </ul>
    </nav>

    <main class="content">
        <h2 id="intro">Introduction</h2>
        <p>Welcome to the documentation...</p>

        <h2 id="setup">Setup</h2>
        <p>To get started, install the package...</p>

        <h2 id="usage">Usage</h2>
        <p>Here are some examples...</p>
    </main>

</div>
```

---

## 8.4 Details and Summary

### Overview

The `<details>` and `<summary>` elements create an interactive collapsible widget that can show or hide content. This is native HTML — no JavaScript required. It is perfect for FAQs, spoilers, code walkthroughs, and progressive disclosure.

### Basic Syntax

```html
<details>
    <summary>Click to expand</summary>
    <p>This content is hidden by default. Click the summary to toggle visibility.</p>
</details>
```

### Open Attribute

The `open` attribute makes the details visible by default:

```html
<details open>
    <summary>Installation Guide</summary>
    <p>Run the following command to install:</p>
    <pre><code>npm install my-package</code></pre>
</details>
```

### Use Case 1: FAQ Sections

```html
<details>
    <summary>What is Markdown?</summary>
    <p>Markdown is a lightweight markup language created by John Gruber in 2004. It allows you to write formatted text using plain text syntax that converts to HTML.</p>
</details>

<details>
    <summary>How do I add images in Markdown?</summary>
    <p>Use the syntax: <code>![alt text](image-url)</code></p>
    <p>Example: <code>![Logo](https://example.com/logo.png)</code></p>
</details>

<details>
    <summary>Can I use HTML inside Markdown?</summary>
    <p>Yes! Markdown supports inline and block-level HTML. You can mix Markdown and HTML freely, though Markdown inside block HTML is not processed by default.</p>
</details>
```

### Use Case 2: Code Walkthroughs

```html
<details>
    <summary>View Solution: Authentication Middleware</summary>

    <h4>Step 1: Define the middleware</h4>
    <pre><code>function authMiddleware(req, res, next) {
    const token = req.headers.authorization;
    if (!token) {
        return res.status(401).json({ error: 'No token provided' });
    }
    next();
}</code></pre>

    <h4>Step 2: Apply to routes</h4>
    <pre><code>app.use('/api/protected', authMiddleware);</code></pre>

    <p><strong>Note:</strong> Always validate tokens server-side.</p>
</details>
```

### Use Case 3: Release Notes

```html
<details>
    <summary>v2.0.0 Release Notes (March 2025)</summary>

    <h4>Breaking Changes</h4>
    <ul>
        <li>Removed deprecated <code>v1/users</code> endpoint</li>
        <li>Changed response format from XML to JSON</li>
    </ul>

    <h4>New Features</h4>
    <ul>
        <li>Added GraphQL API support</li>
        <li>New dashboard with real-time analytics</li>
    </ul>

    <h4>Bug Fixes</h4>
    <ul>
        <li>Fixed memory leak in stream processing</li>
        <li>Fixed race condition in concurrent requests</li>
    </ul>
</details>
```

### Use Case 4: Spoiler Content

```html
<details>
    <summary><strong>Spoiler Alert:</strong> Plot Twist Revealed</summary>
    <p>The protagonist was actually the AI all along. The entire story was a simulation run to test consciousness emergence in artificial systems.</p>
</details>
```

### Styling Details with CSS

```html
<style>
    details {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        background: #fafafa;
    }

    details[open] {
        background: #fff;
        border-color: #007bff;
    }

    summary {
        cursor: pointer;
        font-weight: bold;
        padding: 5px;
        user-select: none;
    }

    summary:hover {
        color: #007bff;
    }

    details[open] summary {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }
</style>
```

### Nested Details

```html
<details>
    <summary>Chapter 1: Getting Started</summary>
    <p>Introduction to the basics...</p>

    <details>
        <summary>1.1 Installation</summary>
        <p>Step-by-step installation guide...</p>
    </details>

    <details>
        <summary>1.2 Configuration</summary>
        <p>How to configure the system...</p>
    </details>
</details>
```

---

## 8.5 Video Embedding

### Overview

The `<video>` element embeds video content directly in HTML pages, with controls for playback, volume, fullscreen, and more. It supports multiple video formats for broad browser compatibility.

### Basic Video Tag

```html
<video controls width="100%" max-width="800">
    <source src="video/tutorial.mp4" type="video/mp4">
    <source src="video/tutorial.webm" type="video/webm">
    Your browser does not support the video element.
</video>
```

### Video Attributes

| Attribute | Values | Description |
|-----------|--------|-------------|
| `controls` | (boolean) | Show playback controls |
| `autoplay` | (boolean) | Start playing automatically |
| `muted` | (boolean) | Mute audio by default |
| `loop` | (boolean) | Loop playback |
| `poster` | URL | Image shown before video starts |
| `preload` | none, metadata, auto | How much to preload |
| `width` | pixels | Video width |
| `height` | pixels | Video height |
| `playsinline` | (boolean) | Play inline (mobile) |

### Complete Examples

```html
<video controls width="100%" poster="thumbnails/tutorial.jpg">
    <source src="videos/tutorial.mp4" type="video/mp4">
    <source src="videos/tutorial.webm" type="video/webm">
    <source src="videos/tutorial.ogg" type="video/ogg">
    <p>Your browser doesn't support HTML video. <a href="videos/tutorial.mp4">Download the video</a>.</p>
</video>
```

### Autoplay (Muted)

```html
<video autoplay muted loop playsinline width="100%">
    <source src="videos/background.mp4" type="video/mp4">
</video>
```

### Subtitles with `<track>`

```html
<video controls width="100%">
    <source src="lecture.mp4" type="video/mp4">
    <track src="subtitles/en.vtt" kind="subtitles" srclang="en" label="English" default>
    <track src="subtitles/es.vtt" kind="subtitles" srclang="es" label="Spanish">
    <track src="subtitles/fr.vtt" kind="subtitles" srclang="fr" label="French">
</video>
```

### Multiple Videos in Documentation

```html
<div class="video-gallery" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <div>
        <h4>Installation Tutorial</h4>
        <video controls width="100%">
            <source src="videos/installation.mp4" type="video/mp4">
        </video>
    </div>
    <div>
        <h4>Configuration Guide</h4>
        <video controls width="100%">
            <source src="videos/configuration.mp4" type="video/mp4">
        </video>
    </div>
    <div>
        <h4>Advanced Features</h4>
        <video controls width="100%">
            <source src="videos/advanced.mp4" type="video/mp4">
        </video>
    </div>
</div>
```

---

## 8.6 Audio Embedding

### Overview

The `<audio>` element embeds audio content directly in HTML pages, with controls for playback, volume, and download.

### Basic Audio Tag

```html
<audio controls>
    <source src="audio/intro.mp3" type="audio/mpeg">
    <source src="audio/intro.ogg" type="audio/ogg">
    <source src="audio/intro.wav" type="audio/wav">
    Your browser does not support the audio element.
</audio>
```

### Audio Attributes

| Attribute | Values | Description |
|-----------|--------|-------------|
| `controls` | (boolean) | Show playback controls |
| `autoplay` | (boolean) | Start playing automatically |
| `muted` | (boolean) | Start muted |
| `loop` | (boolean) | Loop playback |
| `preload` | none, metadata, auto | How much to preload |

### Use Cases

Podcast player:

```html
<audio controls style="width: 100%;">
    <source src="podcasts/episode-42.mp3" type="audio/mpeg">
    <a href="podcasts/episode-42.mp3">Download episode (MP3, 45MB)</a>
</audio>
```

Language learning audio:

```html
<figure>
    <figcaption>Pronunciation: "Bonjour"</figcaption>
    <audio controls src="audio/french/bonjour.mp3">
        <a href="audio/french/bonjour.mp3">Download audio</a>
    </audio>
</figure>
```

Audio playlist:

```html
<div class="playlist">
    <div>
        <strong>Track 1:</strong> Introduction
        <audio controls src="audio/track1.mp3"></audio>
    </div>
    <div>
        <strong>Track 2:</strong> Core Concepts
        <audio controls src="audio/track2.mp3"></audio>
    </div>
    <div>
        <strong>Track 3:</strong> Advanced Topics
        <audio controls src="audio/track3.mp3"></audio>
    </div>
</div>
```

---

## 8.7 HTML Tables

### Overview

While Markdown has a basic table syntax (using pipes and dashes), HTML tables offer far more control over layout, spanning, grouping, and styling.

### Basic HTML Table

```html
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Version</th>
            <th>Released</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Mermaid</td>
            <td>11.0</td>
            <td>2025-01-15</td>
            <td>Stable</td>
        </tr>
        <tr>
            <td>KaTeX</td>
            <td>0.16.11</td>
            <td>2024-11-20</td>
            <td>Stable</td>
        </tr>
    </tbody>
</table>
```

### Column Spanning (colspan)

```html
<table border="1" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="4">Quarterly Report Q1 2025</th>
    </tr>
    <tr>
        <th>Product</th>
        <th>Revenue</th>
        <th>Costs</th>
        <th>Profit</th>
    </tr>
    <tr>
        <td>Product A</td>
        <td>$50,000</td>
        <td>$30,000</td>
        <td>$20,000</td>
    </tr>
    <tr>
        <td>Product B</td>
        <td>$75,000</td>
        <td>$45,000</td>
        <td>$30,000</td>
    </tr>
    <tr>
        <th colspan="3">Total Profit</th>
        <td><strong>$50,000</strong></td>
    </tr>
</table>
```

### Row Spanning (rowspan)

```html
<table border="1" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th rowspan="3">Backend</th>
        <td>Node.js</td>
        <td>Express</td>
    </tr>
    <tr>
        <td>Python</td>
        <td>FastAPI</td>
    </tr>
    <tr>
        <td>Java</td>
        <td>Spring Boot</td>
    </tr>
    <tr>
        <th rowspan="2">Frontend</th>
        <td>React</td>
        <td>Next.js</td>
    </tr>
    <tr>
        <td>Vue</td>
        <td>Nuxt</td>
    </tr>
</table>
```

### Column Groups (colgroup)

```html
<table style="width: 100%; border-collapse: collapse;">
    <colgroup>
        <col style="background-color: #f0f0f0; width: 30%;">
        <col style="background-color: #e8f4e8; width: 35%;">
        <col style="background-color: #fce8e8; width: 35%;">
    </colgroup>
    <thead>
        <tr>
            <th>Feature</th>
            <th>Free Plan</th>
            <th>Pro Plan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Users</td>
            <td>Up to 3</td>
            <td>Unlimited</td>
        </tr>
        <tr>
            <td>Storage</td>
            <td>1 GB</td>
            <td>100 GB</td>
        </tr>
        <tr>
            <td>API Access</td>
            <td>100 req/day</td>
            <td>100,000 req/day</td>
        </tr>
    </tbody>
</table>
```

### Table Caption

```html
<table>
    <caption>Table 1: System Requirements Comparison</caption>
    <thead>
        <tr>
            <th>Component</th>
            <th>Minimum</th>
            <th>Recommended</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>CPU</td>
            <td>2 cores</td>
            <td>4+ cores</td>
        </tr>
        <tr>
            <td>RAM</td>
            <td>4 GB</td>
            <td>16 GB</td>
        </tr>
        <tr>
            <td>Disk</td>
            <td>20 GB SSD</td>
            <td>100 GB SSD</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">* Recommended specs ensure optimal performance</td>
        </tr>
    </tfoot>
</table>
```

### Table with thead, tbody, tfoot

```html
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Price</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>001</td><td>Widget A</td><td>$9.99</td><td>150</td></tr>
        <tr><td>002</td><td>Widget B</td><td>$14.99</td><td>75</td></tr>
        <tr><td>003</td><td>Widget C</td><td>$24.99</td><td>200</td></tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total Products: 3</td>
            <td>Total Value: $49.97</td>
            <td>Total Stock: 425</td>
        </tr>
    </tfoot>
</table>
```

---

## 8.8 Iframes

### Overview

The `<iframe>` element embeds another HTML page within the current page. It is commonly used for embedding YouTube videos, CodePen demos, maps, and external tools.

### Basic Iframe

```html
<iframe src="https://example.com" width="800" height="600" title="Example Site"></iframe>
```

### Security with the sandbox Attribute

The `sandbox` attribute restricts what the embedded content can do:

```html
<iframe src="https://example.com" sandbox="allow-scripts allow-same-origin"></iframe>
```

Sandbox values:

| Value | Description |
|-------|-------------|
| (empty) | All restrictions applied |
| `allow-scripts` | Allows JavaScript |
| `allow-same-origin` | Allows same-origin requests |
| `allow-forms` | Allows form submission |
| `allow-popups` | Allows popup windows |
| `allow-modals` | Allows modal dialogs |
| `allow-presentation` | Allows presentation mode |

### Embedding YouTube Videos

```html
<iframe
    width="560"
    height="315"
    src="https://www.youtube.com/embed/dQw4w9WgXcQ"
    title="YouTube video player"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen>
</iframe>
```

With lazy loading:

```html
<iframe
    width="560"
    height="315"
    src="https://www.youtube.com/embed/dQw4w9WgXcQ"
    title="YouTube video"
    loading="lazy"
    allowfullscreen>
</iframe>
```

### Embedding CodePen

```html
<iframe
    height="300"
    style="width: 100%;"
    scrolling="no"
    src="https://codepen.io/username/embed/abc123?default-tab=html%2Cresult"
    frameborder="no"
    loading="lazy"
    allowtransparency="true"
    allowfullscreen="true">
    See the <a href="https://codepen.io/username/pen/abc123">Pen</a> on CodePen.
</iframe>
```

### Embedding Google Maps

```html
<iframe
    width="100%"
    height="450"
    style="border:0; border-radius: 8px;"
    loading="lazy"
    allowfullscreen
    referrerpolicy="no-referrer-when-downgrade"
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.966309591958!2d-74.004256923815!3d40.74075147932714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259bf5ddc7b01%3A0x3b7b5c9e6b4e5c7a!2sNew+York!5e0!3m2!1sen!2sus!4v1">
</iframe>
```

### Embedding External Documentation

```html
<iframe
    src="https://developer.mozilla.org/en-US/docs/Web/HTML/Element/iframe"
    width="100%"
    height="500"
    title="MDN Iframe Documentation"
    sandbox="allow-scripts"
    loading="lazy">
</iframe>
```

### Responsive Iframe Container

```html
<style>
    .iframe-container {
        position: relative;
        overflow: hidden;
        padding-top: 56.25%; /* 16:9 aspect ratio */
        width: 100%;
    }
    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
</style>

<div class="iframe-container">
    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video" allowfullscreen></iframe>
</div>
```

---

## 8.9 Custom Layouts

### CSS Grid with HTML

```html
<style>
    .grid-demo {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 900px;
        margin: 20px 0;
    }
    .grid-demo .card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    .grid-demo .card h4 { margin: 0 0 10px; color: #333; }
    .grid-demo .card p { margin: 0; color: #666; font-size: 14px; }
    .grid-demo .card.featured {
        background: #007bff;
        color: white;
        grid-column: span 2;
    }
    .grid-demo .card.featured h4,
    .grid-demo .card.featured p { color: white; }
</style>

<div class="grid-demo">
    <div class="card">
        <h4>Basic Plan</h4>
        <p>$9/month</p>
    </div>
    <div class="card featured">
        <h4>Pro Plan</h4>
        <p>$29/month - Most Popular!</p>
    </div>
    <div class="card">
        <h4>Enterprise</h4>
        <p>Contact Sales</p>
    </div>
</div>
```

### Flexbox Layout

```html
<style>
    .flex-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .flex-row .item {
        flex: 1;
        min-width: 200px;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }
    .flex-row .item:nth-child(1) { border-top: 4px solid #e74c3c; }
    .flex-row .item:nth-child(2) { border-top: 4px solid #3498db; }
    .flex-row .item:nth-child(3) { border-top: 4px solid #2ecc71; }
</style>

<div class="flex-row">
    <div class="item">
        <h3>Warning</h3>
        <p>This feature is deprecated and will be removed in v3.0.</p>
    </div>
    <div class="item">
        <h3>Info</h3>
        <p>New API endpoints are available in the developer portal.</p>
    </div>
    <div class="item">
        <h3>Success</h3>
        <p>Your deployment was completed successfully.</p>
    </div>
</div>
```

---

## 8.10 Forms

### Overview

HTML forms collect user input inside Markdown content, enabling interactive documentation such as search boxes, feedback forms, configuration generators, and interactive tutorials.

### Basic Form Structure

```html
<form action="/search" method="get">
    <label for="search">Search documentation:</label>
    <input type="text" id="search" name="q" placeholder="Enter search term..." required>
    <button type="submit">Search</button>
</form>
```

### Input Types

```html
<form>
    <!-- Text input -->
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder="Your name" required>
    <br><br>

    <!-- Email input -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="your@email.com" required>
    <br><br>

    <!-- Password input -->
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" minlength="8">
    <br><br>

    <!-- Number input -->
    <label for="age">Age:</label>
    <input type="number" id="age" name="age" min="0" max="150">
    <br><br>

    <!-- Date input -->
    <label for="start">Start date:</label>
    <input type="date" id="start" name="start">
    <br><br>

    <!-- Checkbox -->
    <input type="checkbox" id="subscribe" name="subscribe" checked>
    <label for="subscribe">Subscribe to newsletter</label>
    <br><br>

    <!-- Radio buttons -->
    <fieldset>
        <legend>Select a plan:</legend>
        <input type="radio" id="basic" name="plan" value="basic">
        <label for="basic">Basic ($9/mo)</label><br>
        <input type="radio" id="pro" name="plan" value="pro" checked>
        <label for="pro">Pro ($29/mo)</label><br>
        <input type="radio" id="enterprise" name="plan" value="enterprise">
        <label for="enterprise">Enterprise</label>
    </fieldset>
    <br>

    <!-- Range slider -->
    <label for="volume">Volume:</label>
    <input type="range" id="volume" name="volume" min="0" max="100" value="50">
    <br><br>

    <!-- File upload -->
    <label for="file">Upload file:</label>
    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx">
    <br><br>

    <!-- Submit button -->
    <button type="submit">Submit</button>
</form>
```

### Textarea

```html
<form>
    <label for="feedback">Feedback:</label><br>
    <textarea id="feedback" name="feedback" rows="6" cols="50"
        placeholder="Share your thoughts..." maxlength="1000" required></textarea>
    <br>
    <button type="submit">Send Feedback</button>
</form>
```

### Select Dropdown

```html
<label for="language">Choose a language:</label>
<select id="language" name="language">
    <optgroup label="Popular">
        <option value="javascript">JavaScript</option>
        <option value="python" selected>Python</option>
        <option value="java">Java</option>
    </optgroup>
    <optgroup label="Other">
        <option value="rust">Rust</option>
        <option value="go">Go</option>
        <option value="ruby">Ruby</option>
    </optgroup>
</select>
```

### Fieldset and Legend

```html
<fieldset style="border: 2px solid #007bff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <legend style="font-weight: bold; color: #007bff; padding: 0 10px;">Personal Information</legend>

    <label for="fname">First name:</label>
    <input type="text" id="fname" name="fname" required><br><br>

    <label for="lname">Last name:</label>
    <input type="text" id="lname" name="lname" required><br><br>

    <button type="submit">Save</button>
</fieldset>
```

### Interactive Configuration Generator

```html
<style>
    .config-gen { background: #f8f9fa; padding: 20px; border-radius: 8px; }
    .config-gen label { display: inline-block; min-width: 150px; }
    .config-gen .output { background: #2d2d2d; color: #fff; padding: 15px; border-radius: 5px; font-family: monospace; margin-top: 15px; }
</style>

<div class="config-gen">
    <h3>Server Configuration Generator</h3>

    <label for="port">Port:</label>
    <input type="number" id="port" value="3000" min="1" max="65535"><br>

    <label for="host">Host:</label>
    <input type="text" id="host" value="localhost"><br>

    <label for="debug">Debug mode:</label>
    <input type="checkbox" id="debug" checked><br>

    <label for="log-level">Log level:</label>
    <select id="log-level">
        <option value="debug">Debug</option>
        <option value="info" selected>Info</option>
        <option value="warn">Warning</option>
        <option value="error">Error</option>
    </select><br><br>

    <button onclick="generateConfig()">Generate Config</button>

    <div class="output" id="config-output">
        {<br>
        &nbsp;&nbsp;"port": 3000,<br>
        &nbsp;&nbsp;"host": "localhost",<br>
        &nbsp;&nbsp;"debug": true,<br>
        &nbsp;&nbsp;"logLevel": "info"<br>
        }
    </div>
</div>

<script>
    function generateConfig() {
        const port = document.getElementById('port').value;
        const host = document.getElementById('host').value;
        const debug = document.getElementById('debug').checked;
        const level = document.getElementById('log-level').value;

        document.getElementById('config-output').innerHTML =
            '{<br>' +
            '&nbsp;&nbsp;"port": ' + port + ',<br>' +
            '&nbsp;&nbsp;"host": "' + host + '",<br>' +
            '&nbsp;&nbsp;"debug": ' + debug + ',<br>' +
            '&nbsp;&nbsp;"logLevel": "' + level + '"<br>' +
            '}';
    }
</script>
```

---

## 8.11 Markdown Inside HTML

### The Problem

In standard CommonMark, Markdown syntax is NOT processed inside block-level HTML elements. This means:

```html
<div>
    **This text will NOT be bold**
    *This text will NOT be italic*
</div>
```

### The markdown=1 Attribute

Some Markdown parsers (including Python-Markdown, PHP Markdown Extra, and GitHub Pages with Jekyll) support the `markdown=1` attribute to enable Markdown processing inside HTML:

```html
<div markdown=1>
    **This text WILL be bold**
    *This text WILL be italic*
</div>
```

### Supported Parser Behaviors

| Parser | markdown=1 | Notes |
|--------|-----------|-------|
| Python-Markdown | Yes | With `md_in_html` extension |
| PHP Markdown Extra | Yes | Native support |
| Kramdown (Jekyll) | Yes | Using `markdown="span"` |
| CommonMark | No | Not supported |
| GitHub Flavored Markdown | No | Not supported |
| Docusaurus MDX | No | Use MDX components instead |
| VitePress | No | Use Vue components instead |

### Workaround: Outer Markdown, Inner HTML

```markdown
**This is bold Markdown**

<div style="padding: 10px; border: 1px solid #ccc;">

**This will NOT be bold in most parsers**

</div>

**This is bold Markdown again**
```

### Workaround: Parser-Specific Solutions

For GitHub Flavored Markdown, use tables or other Markdown-native structures:

```markdown
| Feature | Status |
|---------|--------|
| **Bold text** | Works |
| *Italic text* | Works |
```

### Workaround: Inline HTML for small pieces

Use inline HTML spans instead of block-level divs:

```markdown
<span style="color: red;">**Bold red text** — this works because span is inline HTML</span>
```

### Best Practice Recommendation

When you need Markdown inside HTML containers:

1. Use inline HTML (`<span>`) for small inline formatting with styles
2. Use parser-specific `markdown=1` if available
3. Use plain HTML instead of Markdown inside block HTML
4. Use a different approach (tables, lists) that avoids block HTML
5. For static sites, use the template engine (Liquid, Nunjucks, EJS) instead

---

## 8.12 Security

### XSS Prevention

Cross-Site Scripting (XSS) is one of the most common security vulnerabilities when mixing Markdown and HTML. Attackers can inject malicious scripts through user-generated content that contains HTML.

### Dangerous HTML Patterns

```html
<!-- Script injection -->
<script>alert('XSS')</script>

<!-- Event handlers -->
<img src=x onerror="alert('XSS')">
<a href="javascript:alert('XSS')">Click me</a>

<!-- CSS-based attacks -->
<style>body { display: none; }</style>

<!-- Encoded payloads -->
<a href="&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;">Link</a>
```

### Sanitization with DOMPurify

DOMPurify is a widely-used library for sanitizing HTML:

```html
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
<script>
    const userContent = '<p>Hello <script>alert("xss")</script></p>';
    const cleanContent = DOMPurify.sanitize(userContent);
    // cleanContent = '<p>Hello </p>'
    document.getElementById('output').innerHTML = cleanContent;
</script>
```

```javascript
// Node.js
const createDOMPurify = require('dompurify');
const { JSDOM } = require('jsdom');
const window = new JSDOM('').window;
const DOMPurify = createDOMPurify(window);

const clean = DOMPurify.sanitize(dirty);
```

### Allowed Tags Configuration

DOMPurify allows configuration of allowed tags:

```javascript
const clean = DOMPurify.sanitize(userContent, {
    ALLOWED_TAGS: [
        'b', 'i', 'em', 'strong', 'a', 'p', 'br',
        'ul', 'ol', 'li', 'pre', 'code', 'blockquote',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'img', 'figure', 'figcaption',
        'details', 'summary',
        'div', 'span', 'section', 'article'
    ],
    ALLOWED_ATTR: [
        'href', 'src', 'alt', 'title', 'width', 'height',
        'class', 'id', 'style', 'target', 'rel'
    ],
    ALLOW_DATA_ATTR: false
});
```

### Content Security Policy (CSP)

CSP headers prevent XSS at the browser level:

```http
Content-Security-Policy: default-src 'self';
    script-src 'self' https://cdn.example.com;
    style-src 'self' 'unsafe-inline';
    img-src 'self' https: data:;
    frame-src 'self' https://www.youtube.com;
```

```html
<meta http-equiv="Content-Security-Policy"
    content="default-src 'self';
    script-src 'self';
    style-src 'self' 'unsafe-inline';
    img-src 'self' data:;">
```

### Markdown Parser Security Options

Many Markdown parsers have security options:

**marked.js:**
```javascript
import { marked } from 'marked';

marked.setOptions({
    sanitize: true, // deprecated, use DOMPurify instead
});
```

**markdown-it:**
```javascript
const md = require('markdown-it')({
    html: true, // set to false to disable HTML
    linkify: true,
    typographer: true
});
```

**showdown:**
```javascript
const converter = new showdown.Converter({
    safeMode: true // strips dangerous HTML
});
```

### Security Checklist

- [ ] Sanitize all user-generated HTML with DOMPurify
- [ ] Set a restrictive CSP header
- [ ] Disable HTML in Markdown if not needed
- [ ] Strip `javascript:` URLs from links
- [ ] Remove event handler attributes (`onclick`, `onerror`, etc.)
- [ ] Validate and whitelist allowed tags and attributes
- [ ] Never trust user input
- [ ] Use `sandbox` attribute on iframes
- [ ] Set `rel="noopener noreferrer"` on external links
- [ ] Encode output appropriately for the context

---

## 8.13 Accessibility

### ARIA Roles

ARIA (Accessible Rich Internet Applications) roles provide semantic meaning to HTML elements for assistive technologies:

```html
<nav role="navigation" aria-label="Main navigation">
    <a href="/">Home</a>
    <a href="/docs">Docs</a>
</nav>

<div role="alert">
    <p>Your session will expire in 5 minutes.</p>
</div>

<button role="tab" aria-selected="true" aria-controls="panel1">Tab 1</button>
<div role="tabpanel" id="panel1" aria-labelledby="tab1">
    Tab content here
</div>
```

### Common ARIA Roles

| Role | Purpose | Example |
|------|---------|---------|
| `role="alert"` | Important, time-sensitive message | Error notification |
| `role="button"` | Clickable element | Custom button |
| `role="tablist"` | Container for tabs | Tab navigation |
| `role="tab"` | Individual tab | Tab header |
| `role="tabpanel"` | Tab content | Panel content |
| `role="navigation"` | Navigation links | Menu, breadcrumbs |
| `role="search"` | Search functionality | Search form |
| `role="progressbar"` | Progress indicator | Loading bar |
| `role="tooltip"` | Informational popup | Hint text |
| `role="dialog"` | Modal/popup window | Confirmation dialog |
| `role="status"` | Status message | Live region updates |
| `role="complementary"` | Supporting content | Sidebar |

### ARIA Attributes

```html
<!-- Descriptive labels -->
<button aria-label="Close dialog">X</button>
<input type="text" aria-label="Search" placeholder="Search...">

<!-- Live regions for dynamic content -->
<div aria-live="polite" aria-atomic="true">
    <!-- Screen reader announces changes -->
    <span>3 new messages</span>
</div>

<!-- Expanded state -->
<button aria-expanded="false" aria-controls="menu">Menu</button>
<ul id="menu" hidden>
    <li><a href="/">Home</a></li>
</ul>

<!-- Required fields -->
<label for="email">Email <span aria-hidden="true">*</span></label>
<input type="email" id="email" required aria-required="true">
```

### Semantic HTML Benefits

Using semantic HTML elements improves accessibility naturally:

```html
<!-- Bad: div soup -->
<div class="header">
    <div class="nav">...</div>
</div>
<div class="main">
    <div class="article">...</div>
</div>

<!-- Good: semantic elements -->
<header>
    <nav>...</nav>
</header>
<main>
    <article>...</article>
</main>
```

### Accessible Tables

```html
<table aria-label="Server status overview">
    <caption>Server Monitoring Dashboard</caption>
    <thead>
        <tr>
            <th scope="col">Server</th>
            <th scope="col">Status</th>
            <th scope="col">Uptime</th>
            <th scope="col">Load</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th scope="row">web-01</th>
            <td aria-label="Status: Online">
                <span style="color: green;" aria-hidden="true">&#9679;</span> Online
            </td>
            <td>45 days</td>
            <td>23%</td>
        </tr>
        <tr>
            <th scope="row">db-01</th>
            <td aria-label="Status: Warning">
                <span style="color: orange;" aria-hidden="true">&#9679;</span> Warning
            </td>
            <td>45 days</td>
            <td>78%</td>
        </tr>
    </tbody>
</table>
```

### Accessible Images

```html
<figure>
    <img src="architecture-diagram.png"
         alt="System architecture showing frontend, API gateway, microservices, and database layers"
         width="800" height="600">
    <figcaption>Figure 1: System Architecture Overview</figcaption>
</figure>
```

### Color and Contrast

```html
<!-- Bad: low contrast -->
<span style="color: #cccccc; background: white;">Light gray text</span>

<!-- Good: high contrast -->
<span style="color: #333333; background: #f0f0f0;">Dark text on light background</span>

<!-- Use both visual indicators and text -->
<span style="color: red; font-weight: bold;" aria-label="Error">
    &#9888; Error: Connection failed
</span>
```

### Accessibility Checklist

- [ ] All images have descriptive `alt` text
- [ ] Color is not the only way information is conveyed
- [ ] Text has sufficient contrast ratio (4.5:1 for normal text)
- [ ] Interactive elements have visible focus indicators
- [ ] Form inputs have associated labels
- [ ] ARIA landmarks are used for page structure
- [ ] Tables use `scope` attributes on headers
- [ ] Videos have captions or transcripts
- [ ] Links have descriptive text (not "click here")
- [ ] Dynamic content uses `aria-live` regions
- [ ] Custom controls have proper ARIA roles and properties
- [ ] Keyboard navigation works for all interactive elements

---

## 8.14 Exercises

### Exercise 1: Inline HTML Styling
Write a paragraph that contains a chemical formula (H2O with subscript), a keyboard shortcut (Ctrl+S), and a highlighted term using `<mark>`.

### Exercise 2: Block Layout
Create a two-column layout using `<div>` with CSS Grid or Flexbox that contains a table of contents on the left and content on the right.

### Exercise 3: Collapsible FAQ
Create an FAQ section with three questions using `<details>` and `<summary>`. Topics should be related to Markdown: "What is Markdown?", "How do I add images?", "Does Markdown support HTML?"

### Exercise 4: Video Gallery
Create a responsive grid of three video embeds with titles, using the `<video>` element and appropriate attributes.

### Exercise 5: Complex Table
Build a pricing comparison table with three plans (Basic, Pro, Enterprise) and eight feature rows. Use `colspan`, `rowspan`, `thead`, and `tbody`.

### Exercise 6: Embedded YouTube Video
Embed a YouTube video in a responsive container (16:9 aspect ratio) with proper attributes including `allowfullscreen` and `loading="lazy"`.

### Exercise 7: Configuration Form
Create an interactive API configuration form with fields for endpoint URL, HTTP method selector, authentication type radio buttons, and a textarea for headers.

### Exercise 8: Markdown in HTML Test
Write a section that attempts to use Markdown formatting inside a `<div>` and then uses the `markdown=1` attribute (if supported), explaining why one works and the other does not.

### Exercise 9: Accessible Alert Box
Create a semantic alert box with `role="alert"`, `aria-live="polite"`, proper color contrast, and both visual and textual indicators for the alert type (success, warning, error).

### Exercise 10: Complete Documentation Page
Build a complete documentation page section that combines:
- A semantic `<article>` layout with `<header>`, `<section>`, and `<footer>`
- A collapsible code walkthrough using `<details><summary>`
- An HTML table with API endpoint documentation
- Proper ARIA labels and accessibility attributes
- Inline HTML for code samples and keyboard shortcuts

---

## 8.15 Quiz

### Question 1
Which of the following is NOT a block-level HTML element?
- A) `<div>`
- B) `<span>`
- C) `<section>`
- D) `<article>`

### Question 2
What does the `<kbd>` tag represent?
- A) Keyboard input
- B) Code block
- C) Key definition
- D) Knowledge base

### Question 3
In standard CommonMark, is Markdown processed inside a `<div>`?
- A) Yes, always
- B) No, never
- C) Only with `markdown=1` attribute
- D) Only in GitHub Flavored Markdown

### Question 4
Which attribute on `<details>` makes it visible by default?
- A) `visible`
- B) `show`
- C) `open`
- D) `expanded`

### Question 5
What does the `sandbox` attribute on `<iframe>` do?
- A) Makes the iframe responsive
- B) Applies security restrictions
- C) Adds a border
- D) Enables fullscreen

### Question 6
Which element provides a caption for a `<table>`?
- A) `<legend>`
- B) `<caption>`
- C) `<summary>`
- D) `<figcaption>`

### Question 7
What is the primary security concern when mixing Markdown and HTML?
- A) Slow page loading
- B) Cross-Site Scripting (XSS)
- C) Broken layout
- D) Unclosed tags

### Question 8
Which ARIA role should be used for a modal dialog?
- A) `role="modal"`
- B) `role="dialog"`
- C) `role="popup"`
- D) `role="window"`

### Question 9
What does the `<abbr>` tag require for accessibility?
- A) An `id` attribute
- B) A `title` attribute with the full expansion
- C) A `class` attribute
- D) A `lang` attribute

### Question 10
Which `<video>` attribute is required for autoplay to work in most browsers?
- A) `controls`
- B) `muted`
- C) `loop`
- D) `poster`

### Question 11
What does `colspan="2"` do in an HTML table?
- A) Merges two rows
- B) Merges two columns
- C) Adds two columns
- D) Sets column width to 2

### Question 12
Which HTML element is most appropriate for a quotation or reference?
- A) `<blockquote>`
- B) `<cite>`
- C) `<quote>`
- D) `<ref>`

### Question 13
What is the purpose of DOMPurify?
- A) To clean up CSS styles
- B) To sanitize HTML and prevent XSS
- C) To format HTML code
- D) To validate HTML tags

### Question 14
Which `<source>` attribute specifies the media type for a `<video>` or `<audio>` element?
- A) `type`
- B) `format`
- C) `mime`
- D) `codec`

### Question 15
What does `aria-live="polite"` do?
- A) Makes the element visible
- B) Announces changes to screen readers without interrupting
- C) Adds politeness to the content
- D) Delays the loading of content

---

**Answer Key:**
1. B, 2. A, 3. B, 4. C, 5. B, 6. B, 7. B, 8. B, 9. B, 10. B, 11. B, 12. B, 13. B, 14. A, 15. B
