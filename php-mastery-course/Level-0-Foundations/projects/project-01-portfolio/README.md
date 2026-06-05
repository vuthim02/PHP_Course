# Project 1: Personal Portfolio Website

A fully responsive static portfolio website built with semantic **HTML5** and modern **CSS3** (Flexbox, Grid, CSS custom properties). Demonstrates fundamental web development concepts: document structure, navigation, responsive design, forms, and multi-page architecture — all without JavaScript or a backend.

## Learning Objectives

- Structure a multi-page website with semantic HTML landmarks (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`)
- Style a complete layout using CSS Flexbox and Grid
- Use CSS custom properties (variables) for consistent theming
- Build a responsive design that adapts from desktop to mobile via media queries
- Create accessible forms, navigation, and interactive states (hover, focus)
- Understand the request lifecycle: linking pages via `<a href>`, browser rendering, and server file serving
- Set up a local dev environment (VS Code, Live Server, or direct file open)

## Features

| Feature | Description |
|---------|-------------|
| **Landing Hero** | Full-width intro with avatar, call-to-action buttons |
| **Skills Grid** | Responsive four-column card layout with hover effects |
| **Project Gallery** | Three featured project cards with tags and links |
| **Contact Form** | Accessible form with validation, labels, and placeholders |
| **About Page** | Text-heavy layout with sidebar quick-facts card |
| **Sticky Navigation** | Header with `position: sticky` and active-page indicator |
| **Mobile First** | Single breakpoint at 768px; fully usable on any screen size |

## How to Run

You can serve this project **three ways**:

### 1. Direct File Open (no server)
Double-click `index.html` or open it in your browser via `File > Open`. Forms will not submit (no backend), but layout, navigation, and styling work fully.

### 2. VS Code Live Server
Install the *Live Server* extension, right-click `index.html` > *Open with Live Server*. The page auto-reloads on save — ideal for development.

### 3. PHP Built-in Server (if PHP is installed)

```bash
cd project-01-portfolio
php -S localhost:8080
```

Then visit `http://localhost:8080`.

## Expected Output

A clean, professional single-page portfolio with:

- A top navigation bar linking "Home", "About", "Projects", "Contact"
- A hero section with name, tagline, and two buttons
- A skills section with four cards (Frontend, Backend, DevOps, Design)
- A projects section with three cards showing placeholder images and tech tags
- A contact form with name, email, and message fields
- A footer with copyright and social links
- The About page (`/about.html`) has two-column layout: bio text on left, quick-facts sidebar on right
- All pages are responsive: on mobile ( < 768px ), everything stacks vertically, centered

## Code Structure

```
project-01-portfolio/
├── index.html          # Home page — hero, skills, projects, contact
├── about.html          # About page — bio, story, philosophy, sidebar
├── css/
│   └── style.css       # All styles (single stylesheet)
└── images/             # (placeholder — add your own images)
```

### File Responsibilities

- **`index.html`** — Main landing page entry point. Contains four sections (`#hero`, `#skills`, `#projects`, `#contact`).
- **`about.html`** — Secondary page with extended bio and sidebar card.
- **`css/style.css`** — Single global stylesheet. Uses `:root` custom properties for theming, Flexbox for nav/footer, CSS Grid for skills/projects layout, and a `@media (max-width: 768px)` breakpoint for responsive stacking.
- **`images/`** — Directory ready for profile photo, project screenshots, or favicon.

## Concepts Practiced

| Concept | How It's Used |
|---------|---------------|
| **How computers work** | Files are served from disk to browser via HTTP (or `file://` protocol) |
| **Operating Systems** | File system hierarchy, path navigation (`css/style.css`) |
| **Browsers** | Rendering engine parses HTML → CSSOM → Render Tree → Paint |
| **HTTP** | Each page/view is a separate HTTP GET request (served by Live Server or PHP) |
| **Dev Environment** | Setting up VS Code, Live Server, browser DevTools inspection |
| **HTML5** | Semantic tags, attributes, forms, accessibility (`aria-label`) |
| **CSS3** | Flexbox, Grid, custom properties, transitions, media queries |
| **Responsive Design** | Mobile-first layout with single breakpoint |
| **Version Control (Git)** | This project should be initialized with `git init` and committed |
