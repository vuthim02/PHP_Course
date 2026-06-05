# Module 9: Modern Documentation Systems

> **Duration:** 4 hours | **Level:** Intermediate | **Prerequisites:** Modules 1-8

---

## 9.1 Introduction to Documentation Systems

Documentation systems are integrated platforms, workflows, and toolchains for creating, managing, publishing, and maintaining documentation. They have evolved from static HTML pages to dynamic, collaborative systems that treat documentation as code.

### 9.1.1 What Is a Documentation System?

A documentation system encompasses:

| Component | Description | Examples |
|-----------|-------------|----------|
| Authoring Environment | Where docs are written | VS Code, Obsidian, Notion |
| Format | How content is structured | Markdown, MDX, AsciiDoc, RST |
| Build System | How docs are compiled | VitePress, Docusaurus, MkDocs |
| Hosting | Where docs are served | GitHub Pages, Netlify, Vercel |
| Search | How users find content | Algolia, Pagefind, Meilisearch |
| Versioning | How versions are managed | Git tags, Docusaurus versions |
| Collaboration | How teams contribute | Git, GitHub, review workflows |

### 9.1.2 The Evolution of Documentation

```mermaid
timeline
    title Evolution of Documentation Systems
    1990s : Static HTML pages : Hand-coded : No versioning
    2000s : Wiki systems (MediaWiki) : CMS-based : Basic search
    2010s : ReadTheDocs : Sphinx/AsciiDoc : Versioned docs
    2015  : GitHub Pages + Jekyll : Markdown-first : SSGs emerge
    2018  : Docusaurus 1 : React-based : Plugin ecosystem
    2020  : VitePress : Vue-based : MDX 2 : Modern SSGs
    2023  : Astro Starlight : Multi-framework : Islands architecture
    2025+ : AI-integrated : Real-time collaboration : Intelligent search
```

### 9.1.3 Why Documentation Systems Matter

1. **Consistency** — Templates and components enforce uniform structure
2. **Efficiency** — Reusable content, automated builds, CI/CD pipelines
3. **Collaboration** — Git-based workflows, code review, contributions
4. **Versioning** — Multiple versions maintained simultaneously
5. **Discoverability** — Search, navigation, cross-references
6. **Maintainability** — Single source of truth, content reuse
7. **Quality** — Automated checks, linting, link validation
8. **Analytics** — Usage tracking, search queries, feedback loops
9. **Accessibility** — Built-in a11y checks, screen reader support
10. **Internationalization** — Multi-language deployment workflows

### 9.1.4 Documentation as Code (DaC)

The principle that documentation follows the same lifecycle as software code:

```
Source Control (Git)
    ├── Feature Branch (feature/add-new-api)
    │   ├── Code changes
    │   ├── Test updates
    │   └── Documentation updates ← Docs live WITH code
    ├── Pull Request
    │   ├── Code review
    │   ├── Doc review         ← Docs reviewed like code
    │   └── CI checks (lint, build, link check)
    └── Merge → Deploy
        ├── Production deploy
        └── Docs deploy        ← Docs deploy with code
```

---

## 9.2 GitHub Documentation Ecosystem

GitHub provides a complete documentation ecosystem natively.

### 9.2.1 README.md Best Practices

The README is the most important document in any repository. It is the first thing users see.

**Essential README Sections:**

| Section | Purpose | Required? |
|---------|---------|-----------|
| Project Name & Badges | Identity, CI status, coverage | Yes |
| Description | 2-3 sentence explanation | Yes |
| Table of Contents | Navigation for long READMEs | Recommended |
| Installation | Step-by-step setup | Yes |
| Quick Start | Minimal working example | Yes |
| Usage | Common operations | Yes |
| API Reference | Key functions/endpoints | For libraries |
| Configuration | Environment variables, options | For apps |
| Examples | Multiple use-case demonstrations | Recommended |
| Contributing | How to contribute | Yes |
| License | Legal information | Yes |
| Acknowledgments | Credits, inspirations | Optional |

**Complete README Template:**

```markdown
# Project Name

[![CI](https://github.com/user/project/actions/workflows/ci.yml/badge.svg)](https://github.com/user/project/actions/workflows/ci.yml)
[![npm version](https://img.shields.io/npm/v/project.svg)](https://www.npmjs.com/package/project)
[![Coverage](https://codecov.io/gh/user/project/branch/main/graph/badge.svg)](https://codecov.io/gh/user/project)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A brief, powerful description of what this project does and why it exists.
Solve [specific problem] for [target audience].

## Features

- Feature 1: One-line description
- Feature 2: One-line description
- Feature 3: One-line description

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
- [API](#api)
- [Configuration](#configuration)
- [Examples](#examples)
- [Contributing](#contributing)
- [License](#license)

## Installation

```bash
npm install project-name
```

## Quick Start

```javascript
import { createApp } from 'project-name';

const app = createApp({
  port: 3000,
});

app.start();
// → Server running on http://localhost:3000
```

## Usage

### Basic Operations

```javascript
// Example 1: Basic usage
const result = project.doSomething('input');
console.log(result);
// → 'expected output'
```

### Advanced Usage

```javascript
// Example 2: Advanced configuration
const config = {
  optionA: true,
  optionB: 'custom',
  optionC: 42,
};
project.configure(config);
```

## API

### `createApp(options)`

Creates a new application instance.

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `options.port` | `number` | `8080` | Server port |
| `options.host` | `string` | `'localhost'` | Server hostname |
| `options.ssl` | `boolean` | `false` | Enable HTTPS |

**Returns:** `App` instance

### `app.start()`

Starts the application server. Returns a Promise that resolves when
the server is listening.

## Configuration

| Environment Variable | Default | Description |
|--------------------|---------|-------------|
| `PORT` | `8080` | Server port |
| `HOST` | `localhost` | Server hostname |
| `LOG_LEVEL` | `info` | Logging level |

## Examples

### Example 1: REST API Server

```javascript
import { createApp } from 'project-name';

const app = createApp({ port: 3000 });

app.get('/api/hello', (req, res) => {
  res.json({ message: 'Hello, World!' });
});

app.start();
```

### Example 2: WebSocket Server

```javascript
import { createApp } from 'project-name';

const app = createApp({ port: 3000 });

app.on('connection', (socket) => {
  socket.send('Welcome!');
});

app.start();
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our
code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see
[LICENSE](LICENSE) for details.
```

### 9.2.2 GitHub Wiki

GitHub Wiki provides simple, Git-backed documentation:

- Supports Markdown rendering
- Has its own Git repository
- Supports sidebars (`_Sidebar.md`) and footers (`_Footer.md`)
- No access control per page (public or private per repo)
- Revision history for each page
- Search functionality

**When to use GitHub Wiki vs Dedicated Docs:**

| Factor | GitHub Wiki | Dedicated Site |
|--------|------------|----------------|
| Setup time | Minutes | Hours-days |
| Customization | Limited | Full |
| Search | Basic | Advanced (Algolia) |
| Versioning | Per-page history | Full version |
| Analytics | None | Full |
| Access control | Per-repo | Per-page/group |
| Custom domain | No | Yes |
| Good for | Small projects, internal | Large products, public |

### 9.2.3 GitHub Pages

GitHub Pages hosts static sites directly from repositories.

**Supported Static Site Generators:**

| Generator | Language | Config File | Build Command |
|-----------|----------|-------------|---------------|
| Jekyll | Ruby | `_config.yml` | `jekyll build` |
| Hugo | Go | `config.toml` | `hugo` |
| VitePress | Node.js | `.vitepress/config.ts` | `vitepress build` |
| Docusaurus | Node.js | `docusaurus.config.js` | `npm run build` |
| MkDocs | Python | `mkdocs.yml` | `mkdocs build` |
| Astro | Node.js | `astro.config.mjs` | `npm run build` |

**GitHub Actions for Pages Deployment:**

```yaml
name: Deploy Documentation

on:
  push:
    branches: [main]
    paths:
      - 'docs/**'
      - 'mkdocs.yml'

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Set up Python
        uses: actions/setup-python@v5
        with:
          python-version: '3.12'

      - name: Install dependencies
        run: |
          pip install mkdocs-material
          pip install mkdocs-git-revision-date-plugin
          pip install mkdocs-minify-plugin

      - name: Build documentation
        run: mkdocs build

      - name: Upload Pages artifact
        uses: actions/upload-pages-artifact@v3
        with:
          path: 'site/'

      - name: Deploy to GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
```

### 9.2.4 GitHub Actions for Documentation Automation

**Common Documentation Workflows:**

```yaml
name: Documentation CI

on:
  pull_request:
    paths:
      - 'docs/**'
      - '*.md'

jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Check Markdown links
        uses: gaurav-nelson/github-action-markdown-link-check@v1
        with:
          use-quiet-mode: 'yes'
          config-file: '.mlc-config.json'

      - name: Lint Markdown
        uses: DavidAnson/markdownlint-cli2-action@v15
        with:
          globs: '**/*.md'

      - name: Spell check
        uses: streetsidesoftware/cspell-action@v6
        with:
          files: 'docs/**/*.md'

  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Build docs
        run: |
          pip install mkdocs-material
          mkdocs build --strict

      - name: Validate build output
        run: |
          test -f site/index.html
          echo "Build successful"
```

### 9.2.5 GitHub Discussions for Community Documentation

Discussions serve as living documentation:

```
Discussions Categories
├── Q&A
│   ├── Frequently asked questions
│   └── Common solutions (curated into docs)
├── Show and tell
│   └── Community examples (referenced in docs)
├── Ideas
│   └── Feature requests (influence roadmap docs)
└── General
    └── Community guides (linked from docs)
```

### 9.2.6 Issue and PR Templates

**Issue Template for Documentation Request:**

```markdown
---
name: Documentation Request
about: Suggest improvements to documentation
title: '[DOCS] '
labels: documentation
assignees: ''
---

## What needs documentation?

A clear description of the feature, concept, or process that needs
documentation.

## Who is the audience?

- [ ] End users
- [ ] Developers
- [ ] Administrators
- [ ] Contributors

## Where should this be documented?

- [ ] README
- [ ] API reference
- [ ] Tutorial
- [ ] Migration guide
- [ ] Troubleshooting guide

## Additional context

Add any other context, screenshots, or examples here.
```

### 9.2.7 Case Study: docs.github.com

GitHub's own documentation site exemplifies modern documentation:

```mermaid
graph TB
    subgraph "Content Sources"
        A[Markdown files in repo]
        B[OpenAPI specs]
        C[Community contributions]
        D[Translations]
    end

    subgraph "Build Pipeline"
        E[GitHub Actions]
        F[Jekyll build]
        G[Link checker]
        H[Accessibility check]
    end

    subgraph "Delivery"
        I[CDN]
        J[Search index]
        K[PDF generation]
    end

    subgraph "Platform"
        L[docs.github.com]
        M[Algolia Search]
        N[Feedback system]
    end

    A --> E
    B --> E
    C --> E
    D --> E
    E --> F
    F --> G
    F --> H
    G --> I
    H --> I
    F --> J
    F --> K
    I --> L
    J --> M
    L --> N
```

**Key Architecture Decisions:**

1. **Single source of truth** — All content in version-controlled Markdown
2. **OpenAPI integration** — API docs auto-generated from specs
3. **Community contributions** — Edit button links to source on GitHub
4. **Translation workflow** — Crowdin integration with automated PRs
5. **Feature flags** — Content gated behind product flags
6. **Analytics-informed** — Popular pages optimized, low-traffic reviewed

---

## 9.3 GitLab Documentation

GitLab's approach centers on "handbook-first" and "documentation as code."

### 9.3.1 GitLab Pages

```yaml
# .gitlab-ci.yml for documentation
pages:
  stage: deploy
  image: python:3.11
  script:
    - pip install mkdocs-material
    - pip install mkdocs-git-revision-date-localized-plugin
    - pip install mkdocs-minify-plugin
    - pip install mkdocs-redirects
    - mkdocs build --strict
    - mv site public
  artifacts:
    paths:
      - public
  only:
    - main
  environment:
    name: production
    url: https://example.gitlab.io/project
```

### 9.3.2 Handbook-First Approach

GitLab's handbook is publicly available and documents everything:

| Principle | Description | Implementation |
|-----------|-------------|----------------|
| Handbook-first | Document before discussing | Merge request with docs before verbal decisions |
| Single source | Company knowledge in one place | handbook.gitlab.com |
| Everyone can contribute | Low barrier to editing | Direct link to edit on every page |
| Transparent by default | Public unless confidential | Handbook is public |
| Living document | Constantly updated | Automated merge requests for stale content |

### 9.3.3 Documentation as Code in GitLab

```yaml
# Complete GitLab CI/CD for documentation pipeline
stages:
  - validate
  - build
  - deploy

markdown-lint:
  stage: validate
  image: node:20
  script:
    - npm install -g markdownlint-cli
    - markdownlint 'docs/**/*.md' --config .markdownlint.json

link-check:
  stage: validate
  image: alpine:latest
  script:
    - apk add --no-cache curl
    - find docs -name '*.md' -exec sh -c '
        for file; do
          echo "Checking links in $file..."
          grep -oP "(https?://[^\s\)\]]+)" "$file" |
          while read -r url; do
            if ! curl -sf "$url" > /dev/null 2>&1; then
              echo "BROKEN: $url in $file"
              exit 1
            fi
          done
        done
      ' sh {} +

vale-lint:
  stage: validate
  image: jdkato/vale:latest
  script:
    - vale docs/
  variables:
    VALE_CONFIG: .vale.ini

build-docs:
  stage: build
  image: python:3.11
  script:
    - pip install mkdocs-material mkdocs-git-revision-date-localized-plugin
    - mkdocs build --strict
  artifacts:
    paths:
      - site/
    expire_in: 1 week

deploy-pages:
  stage: deploy
  script:
    - mv site/ public/
  artifacts:
    paths:
      - public/
  environment:
    name: pages
  only:
    - main
```

---

## 9.4 Developer Documentation

Developer documentation serves engineers who integrate with or extend a product.

### 9.4.1 API Reference Documentation (OpenAPI/Swagger)

```yaml
openapi: 3.1.0
info:
  title: Payment API
  version: 2.0.0
  description: |
    # Payment API v2

    Process payments, manage subscriptions, and handle refunds.

    ## Authentication

    All requests require an API key in the `Authorization` header:
    ```
    Authorization: Bearer sk_live_abc123
    ```
  contact:
    name: Developer Support
    url: https://docs.example.com/support
    email: dev@example.com

servers:
  - url: https://api.example.com/v2
    description: Production
  - url: https://sandbox.api.example.com/v2
    description: Sandbox

paths:
  /payments:
    post:
      summary: Create a payment
      description: |
        Creates a new payment and returns the payment intent.

        Use this endpoint to initiate a payment with a payment method ID
        obtained from the client-side SDK.

        **Idempotency:** This endpoint supports idempotency keys.
        Send an `Idempotency-Key` header to prevent duplicate charges.
      operationId: createPayment
      tags:
        - Payments
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CreatePaymentRequest'
            example:
              amount: 2000
              currency: usd
              payment_method: pm_card_visa
              description: "Test payment"
              metadata:
                order_id: "ORD-12345"
      responses:
        '201':
          description: Payment created successfully
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/Payment'
        '400':
          description: Invalid request
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/Error'
        '402':
          description: Payment failed
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/PaymentFailure'

components:
  schemas:
    CreatePaymentRequest:
      type: object
      required:
        - amount
        - currency
        - payment_method
      properties:
        amount:
          type: integer
          description: Amount in smallest currency unit (cents)
          minimum: 50
          maximum: 99999999
          example: 2000
        currency:
          type: string
          description: Three-letter ISO currency code
          pattern: '^[a-z]{3}$'
          example: usd
        payment_method:
          type: string
          description: Payment method ID from client SDK
          example: pm_card_visa
        description:
          type: string
          maxLength: 255
          example: "Test payment"
        metadata:
          type: object
          description: Key-value metadata (max 50 keys)
          additionalProperties:
            type: string
            maxLength: 500
```

### 9.4.2 SDK and Client Library Documentation

**Structure of a Great SDK Doc Page:**

```markdown
# JavaScript SDK

## Installation

```bash
npm install @company/sdk
```

## Quick Start

```javascript
import { CompanyClient } from '@company/sdk';

const client = new CompanyClient({
  apiKey: process.env.COMPANY_API_KEY,
  environment: 'sandbox',
});

// Create a payment
const payment = await client.payments.create({
  amount: 2000,
  currency: 'usd',
  paymentMethod: 'pm_card_visa',
});

console.log(payment.id);
// → 'pi_abc123'
```

## Authentication

The SDK supports multiple authentication methods:

| Method | Environment Variable | Constructor Option |
|--------|---------------------|-------------------|
| API Key | `COMPANY_API_KEY` | `apiKey` |
| OAuth Token | `COMPANY_OAUTH_TOKEN` | `accessToken` |
| Client Certificate | `COMPANY_CERT_PATH` | `certPath` |

## Error Handling

```javascript
import { CompanyClient, CompanyError } from '@company/sdk';

try {
  await client.payments.create({/* ... */});
} catch (error) {
  if (error instanceof CompanyError) {
    switch (error.code) {
      case 'insufficient_funds':
        console.log('Please add funds to your account.');
        break;
      case 'card_declined':
        console.log('Card was declined. Try a different card.');
        break;
      default:
        console.log(`Error: ${error.message}`);
    }
  }
}
```

## Pagination

All list methods return paginated results:

```javascript
// Auto-pagination
for await (const payment of client.payments.list()) {
  console.log(payment.id);
}

// Manual pagination
const page = await client.payments.list({ limit: 10 });
console.log(page.data);       // Array of payments
console.log(page.hasMore);    // boolean
console.log(page.nextCursor); // string | null

const nextPage = await client.payments.list({
  limit: 10,
  cursor: page.nextCursor,
});
```

## Webhooks

```javascript
import { CompanyWebhooks } from '@company/sdk';

// Verify webhook signature
const event = CompanyWebhooks.constructEvent(
  req.body,
  req.headers['stripe-signature'],
  process.env.WEBHOOK_SECRET
);

switch (event.type) {
  case 'payment.succeeded':
    console.log('Payment succeeded:', event.data.object.id);
    break;
  case 'payment.failed':
    console.log('Payment failed:', event.data.object.id);
    break;
}
```

## TypeScript Support

```typescript
import { CompanyClient, Payment, PaymentCreateParams } from '@company/sdk';

const client = new CompanyClient({ apiKey: 'sk_test_...' });

const params: PaymentCreateParams = {
  amount: 2000,
  currency: 'usd',
  paymentMethod: 'pm_card_visa',
};

const payment: Payment = await client.payments.create(params);
```

### Rate Limiting

| Plan | Requests/second | Burst |
|------|----------------|-------|
| Free | 10 | 20 |
| Pro | 100 | 200 |
| Enterprise | 1000 | 2000 |

Retry with exponential backoff:

```javascript
import { CompanyClient } from '@company/sdk';
import { backOff } from 'exponential-backoff';

const client = new CompanyClient({ apiKey: 'sk_test_...' });

const result = await backOff(
  () => client.payments.create(params),
  {
    numOfAttempts: 3,
    retry: (err) => err.status === 429,
  }
);
```
```

### 9.4.3 Code Comments to Documentation Pipeline

```mermaid
flowchart LR
    A[Source Code] --> B[Documentation Comments]
    B --> C[Doc Generator]
    C --> D[API Reference]
    C --> E[Type Definitions]
    C --> F[Usage Examples]
    
    G[Inline Tests] --> H[Extract Examples]
    H --> I[Test Examples]
    I --> J[Verified Examples]
    J --> K[Add to Docs]
    
    subgraph Tools
        L[TSDoc/TypeDoc]
        M[JSDoc]
        N[Docstring/Sphinx]
        O[rustdoc]
    end
```

**Example: TSDoc for TypeScript:**

```typescript
/**
 * Creates a new payment and returns the payment intent object.
 *
 * @remarks
 * This method initializes a payment with the provided parameters.
 * It returns a `PaymentIntent` object that can be used on the
 * client side to confirm the payment.
 *
 * @example
 * ```typescript
 * const payment = await api.createPayment({
 *   amount: 2000,
 *   currency: 'usd'
 * });
 * console.log(payment.id); // 'pi_xxx'
 * ```
 *
 * @param params - The payment creation parameters
 * @param options - Optional request configuration
 * @returns A promise that resolves with the PaymentIntent
 * @throws {@link ApiError} when the request fails
 *
 * @beta This method may change in future versions
 */
async function createPayment(
  params: CreatePaymentParams,
  options?: RequestOptions
): Promise<PaymentIntent> {
  const response = await this.post('/v1/payments', params, options);
  return new PaymentIntent(response.data);
}
```

### 9.4.4 Tutorials and Getting Started Guides

**5-Minute Quickstart Tutorial Structure:**

```
GETTING STARTED
├── Prerequisites
│   ├── What you need installed
│   ├── Required accounts
│   └── Environment setup
├── Step 1: Installation
│   ├── Package manager command
│   └── Verify installation
├── Step 2: Configuration
│   ├── API key setup
│   └── Environment variables
├── Step 3: First Request
│   ├── Minimal code example
│   └── Expected output
├── Step 4: Next Steps
│   └── Links to deeper tutorials
└── Troubleshooting
    ├── Common errors
    └── Getting help
```

### 9.4.5 Migration Guides

**Migration Guide Template:**

```markdown
# Migrating from v1 to v2

## Why Upgrade?

- 3x faster performance
- TypeScript-native types
- Simplified API surface
- Better error messages

## Breaking Changes

### 1. Constructor Signature

**v1 (old):**
```javascript
const client = new CompanyClient('sk_xxx');
```

**v2 (new):**
```javascript
const client = new CompanyClient({ apiKey: 'sk_xxx' });
```

**Migration:**
```javascript
// Before
const client = new CompanyClient('sk_xxx');

// After
const client = new CompanyClient({ apiKey: 'sk_xxx' });
```

### 2. Payment Creation

**v1 (old):**
```javascript
const payment = await client.createPayment(2000, 'usd');
```

**v2 (new):**
```javascript
const payment = await client.payments.create({
  amount: 2000,
  currency: 'usd',
});
```

### 3. Error Handling

**v1 (old):**
```javascript
try {
  await client.createPayment(2000, 'usd');
} catch (err) {
  console.log(err.message);
}
```

**v2 (new):**
```javascript
try {
  await client.payments.create({ amount: 2000, currency: 'usd' });
} catch (err) {
  if (err instanceof ApiError) {
    console.log(err.code, err.details);
  }
}
```

### 4. Removed Features

| Feature | v1 | v2 | Alternative |
|---------|----|----|-------------|
| Callbacks | `callback` param | Removed | Use Promises |
| CSV export | `exportCSV()` | Removed | Use API endpoint |
| Old webhooks | Legacy format | Removed | Upgrade webhook endpoints |

## Timeline

| Date | Event |
|------|-------|
| Jan 2025 | v2 GA release |
| Mar 2025 | v1 deprecation notice |
| Jun 2025 | v1 enters maintenance |
| Dec 2025 | v1 end of life |

## Need Help?

- [Migration tool](https://github.com/company/migration-tool)
- [Migration workshop recording](https://youtube.com/...)
- [Community forum](https://community.company.com)
```

### 9.4.6 Changelogs and Release Notes

**Keep a Changelog Format:**

```markdown
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [2.1.0] - 2025-03-15

### Added

- New `webhooks.list()` method for retrieving webhook history
- TypeScript generics for `createPayment<T>()` type safety
- `sandbox` environment flag in constructor

### Changed

- Rate limits increased from 100 to 200 requests/second
- Error messages now include `requestId` for debugging
- Improved pagination performance (3x faster)

### Deprecated

- `client.createPayment()` in favor of `client.payments.create()`

### Removed

- Node.js 14 support (minimum Node.js 18)

### Fixed

- Race condition in webhook signature verification
- Memory leak in long-running connections
- Incorrect TypeScript types for `list()` methods

### Security

- Updated dependencies to patch CVE-2025-12345

## [2.0.0] - 2025-01-20

See [Migration Guide](docs/migration-v2.md) for details.

### Added

- Full TypeScript support
- Promise-based API (callbacks removed)
- Automatic pagination with `for await...of`
- Webhook verification helper

### Changed

- Complete API redesign (breaking changes)
- Constructor now accepts config object
- Error handling uses typed error classes

### Removed

- All callback-style methods
- Node.js 12 support
- Legacy CSV export

## [1.5.0] - 2024-11-01

### Added

- Idempotency key support for payment creation
- Metadata field on all resources
- Webhook retry configuration
```

---

## 9.5 Open-Source Documentation

Open-source documentation must accommodate diverse contributors with varying skill levels.

### 9.5.1 Essential Open-Source Documents

```mermaid
graph TB
    subgraph "Repository Documentation"
        A[README.md]
        B[CONTRIBUTING.md]
        C[CODE_OF_CONDUCT.md]
        D[LICENSE]
        E[SECURITY.md]
        F[SUPPORT.md]
    end
    
    subgraph "Governance"
        G[GOVERNANCE.md]
        H[MAINTAINERS.md]
        I[ROADMAP.md]
    end
    
    subgraph "Contributor Workflow"
        J[ISSUE_TEMPLATE]
        K[PULL_REQUEST_TEMPLATE]
        L[CONTRIBUTORS.md]
    end
    
    subgraph "Community"
        M[CHANGELOG.md]
        N[FAQ.md]
        O[Translation Guide]
    end
    
    style A fill:#4CAF50,color:white
    style B fill:#2196F3,color:white
    style C fill:#FF9800,color:white
```

### 9.5.2 CONTRIBUTING.md Template

```markdown
# Contributing to Project Name

First off, thank you for considering contributing! 🎉

## Code of Conduct

This project and everyone participating in it is governed by our
[Code of Conduct](CODE_OF_CONDUCT.md). By participating, you agree
to uphold this code.

## How to Contribute

### 🤔 Have a Question?

- Check the [FAQ](FAQ.md)
- Search existing [issues](https://github.com/user/project/issues)
- Start a [discussion](https://github.com/user/project/discussions)

### 🐛 Found a Bug?

1. **Search** existing issues to check if it's been reported
2. **Create a reproduction** — a minimal code example that demonstrates the bug
3. **Open an issue** using the bug report template

**Good bug report checklist:**

- [ ] Version of the project you're using
- [ ] Node.js/Python/Go version
- [ ] Operating system
- [ ] Expected behavior
- [ ] Actual behavior
- [ ] Minimal reproduction (preferably a link to a repository)
- [ ] Screenshots if applicable

### 💡 Have a Feature Request?

1. Check the [roadmap](ROADMAP.md) to see if it's planned
2. Check existing [feature requests](https://github.com/user/project/labels/enhancement)
3. Open a discussion first to gather feedback
4. Open a feature request issue

### 🔧 Want to Write Code?

#### Getting Started

1. Fork the repository
2. Clone your fork: `git clone https://github.com/your-username/project.git`
3. Set up upstream: `git remote add upstream https://github.com/user/project.git`
4. Create a branch: `git checkout -b feature/your-feature-name`
5. Install dependencies: `npm install`
6. Make your changes
7. Run tests: `npm test`
8. Run linter: `npm run lint`
9. Commit your changes (see commit conventions below)
10. Push to your fork: `git push origin feature/your-feature-name`
11. Open a Pull Request

#### Development Setup

```bash
# Clone the repository
git clone https://github.com/user/project.git
cd project

# Install dependencies
npm install

# Run in development mode
npm run dev

# Run tests in watch mode
npm run test:watch
```

#### Project Structure

```
project/
├── src/              # Source code
│   ├── components/   # UI components
│   ├── utils/        # Utility functions
│   └── types/        # TypeScript types
├── tests/            # Test files
├── docs/             # Documentation source
│   ├── guides/       # Usage guides
│   └── api/          # API reference
├── examples/         # Example projects
└── scripts/          # Build/release scripts
```

### 📝 Want to Improve Documentation?

Documentation improvements are extremely valuable! You can:

1. Fix typos or unclear explanations
2. Add code examples
3. Write tutorials
4. Translate documentation
5. Improve API reference docs

Documentation is in the `docs/` directory. To preview:

```bash
npm run docs:dev
```

### 🌐 Translation Guide

We welcome translations! See [TRANSLATION.md](TRANSLATION.md) for details.

## Commit Conventions

We use [Conventional Commits](https://www.conventionalcommits.org/):


<type>(<scope>): <description>

[optional body]

[optional footer]


**Types:**

| Type | Description |
|------|-------------|
| `feat` | New feature |
| `fix` | Bug fix |
| `docs` | Documentation only |
| `style` | Formatting, no code change |
| `refactor` | Code restructuring |
| `test` | Adding/editing tests |
| `chore` | Maintenance, tooling |

**Examples:**


feat(payments): add idempotency key support
fix(api): handle rate limiting gracefully
docs: update quickstart guide
test(webhooks): add signature verification tests


## Pull Request Process

1. Ensure all tests pass: `npm test`
2. Ensure linting passes: `npm run lint`
3. Update documentation if needed
4. Add tests for new functionality
5. Update the changelog
6. Your PR must be reviewed by at least one maintainer

### PR Title Format

Use the same convention as commits:


feat(payments): add idempotency key support


### PR Checklist

- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Changelog entry added
- [ ] Commits are squashed
- [ ] Branch is up to date with main

## Release Process

1. Maintainer creates a release branch
2. Version is bumped according to semver
3. Changelog is finalized
4. Release is tagged and published
5. Release notes are posted on GitHub

## Getting Help

- GitHub Discussions: https://github.com/user/project/discussions
- Discord: https://discord.gg/project
- Twitter: @project

## Recognition

All contributors will be added to our [CONTRIBUTORS.md](CONTRIBUTORS.md)!
```

### 9.5.3 Governance Models

| Model | Description | Examples |
|-------|-------------|----------|
| BDFL | Benevolent Dictator for Life | Python, Linux |
| Meritocracy | Contributors earn authority | Apache projects |
| Foundation | Legal entity oversees project | Kubernetes (CNCF), Node.js (OpenJS) |
| Corporate | Company manages project | React (Meta), VS Code (Microsoft) |
| Community | Collective decision-making | Homebrew, NixOS |

### 9.5.4 Documentation Sprints

A documentation sprint is a focused event (1-5 days) where contributors work exclusively on docs.

**Sprint Organization Checklist:**

```
□ Define scope (which docs, what improvements)
□ Create tracking board (GitHub Projects)
□ Set up documentation environment
□ Create issue templates for sprint tasks
□ Recruit participants (writers, reviewers, subject matter experts)
□ Create style guide reference
□ Set up metrics (pages improved, issues closed)
□ Plan daily standups
□ Schedule review sessions
□ Celebrate and recognize contributors
```

### 9.5.5 Case Studies

#### Kubernetes Documentation

| Metric | Approach |
|--------|----------|
| Site | kubernetes.io |
| Generator | Hugo + Docsy theme |
| Content type | Tasks, concepts, tutorials, reference |
| Localization | 15+ languages |
| Contribution | Web-based editor, PR workflow |
| Review | SIG Docs team, technical review |
| Testing | Netlify preview deployments |

#### React Documentation

| Metric | Approach |
|--------|----------|
| Site | react.dev |
| Generator | Custom framework (based on Next.js) |
| Content type | Tutorial (Tic-Tac-Toe), API reference, guides |
| Interaction | Live code editors (Sandpack) |
| Versioning | Single version (current) |
| Contribution | GitHub discussions, PRs |
| Innovation | Interactive examples, challenges |

#### VS Code Documentation

| Metric | Approach |
|--------|----------|
| Site | code.visualstudio.com/docs |
| Generator | Custom (Azure-based) |
| Content type | Getting started, tasks, customization |
| Search | Built-in + Algolia |
| Versioning | Release-based updates |
| Media | Articles, videos, code samples |
| Integration | In-product links to docs |

---

## 9.6 Software Documentation

### 9.6.1 User Manuals

**Structure:**

```
Product Name User Manual v2.0
├── 1. Introduction
│   ├── 1.1 About This Manual
│   ├── 1.2 System Requirements
│   └── 1.3 Getting Help
├── 2. Installation
│   ├── 2.1 Windows Installation
│   ├── 2.2 macOS Installation
│   ├── 2.3 Linux Installation
│   └── 2.4 Docker Installation
├── 3. Quick Start
│   ├── 3.1 First Launch
│   ├── 3.2 Creating Your First Project
│   └── 3.3 Basic Operations
├── 4. User Interface
│   ├── 4.1 Main Window
│   ├── 4.2 Toolbars
│   ├── 4.3 Panels
│   └── 4.4 Keyboard Shortcuts
├── 5. Features
│   ├── 5.1 Creating Documents
│   ├── 5.2 Editing Content
│   ├── 5.3 Formatting
│   ├── 5.4 Import/Export
│   └── 5.5 Collaboration
├── 6. Configuration
├── 7. Troubleshooting
├── 8. Glossary
└── 9. Index
```

### 9.6.2 Admin Guides

**Admin Guide Sections:**

| Section | Content |
|---------|---------|
| Architecture Overview | System components, data flow |
| Installation | Server requirements, setup steps |
| Configuration | Environment variables, config files |
| Authentication | SSO, LDAP, OAuth setup |
| User Management | Creating users, roles, permissions |
| Monitoring | Logs, metrics, alerts |
| Backup & Restore | Database backups, disaster recovery |
| Scaling | Horizontal/vertical scaling |
| Security | Firewall, encryption, audit |
| Troubleshooting | Common admin issues |

### 9.6.3 Troubleshooting Guide Template

```markdown
# Troubleshooting Guide

## Quick Diagnosis

| Symptom | Likely Cause | Solution |
|---------|-------------|----------|
| App won't start | Port conflict | Change port in config |
| Connection timeout | Firewall rules | Check firewall settings |
| 5xx errors | Database connection | Check database status |
| Slow responses | Memory pressure | Increase memory limit |

## Error Codes

### E1001: Database Connection Failed

**Symptoms:**
- Application fails to start
- 500 errors on database queries

**Causes:**
1. Database server is not running
2. Wrong credentials in configuration
3. Network connectivity issue
4. Database reached connection limit

**Resolution:**

1. Check database status:
   ```bash
   systemctl status postgresql
   ```

2. Test database connection:
   ```bash
   psql -h localhost -U app_user -d app_db
   ```

3. Verify configuration:
   ```bash
   grep DATABASE_URL .env
   # Expected: postgresql://user:pass@localhost:5432/app
   ```

4. Check connection pool:
   ```sql
   SELECT count(*) FROM pg_stat_activity;
   ```

### E1002: Rate Limit Exceeded

**Symptoms:**
- 429 Too Many Requests responses
- API calls failing

**Resolution:**

1. Check current rate limit status:
   ```bash
   curl -I https://api.example.com/v1/health
   # Look for X-RateLimit-* headers
   ```

2. Implement exponential backoff:
   ```javascript
   async function fetchWithRetry(url, retries = 3) {
     for (let i = 0; i < retries; i++) {
       try {
         return await fetch(url);
       } catch (err) {
         if (err.status === 429) {
           const wait = Math.pow(2, i) * 1000;
           await new Promise(r => setTimeout(r, wait));
         }
       }
     }
     throw new Error('Max retries exceeded');
   }
   ```

## Common Issues

### Issue: Application crashes on startup

**Debug Steps:**

1. Check logs:
   ```bash
   journalctl -u myapp -n 100 --no-pager
   ```

2. Validate configuration:
   ```bash
   myapp --validate-config
   ```

3. Check dependencies:
   ```bash
   npm ls
   # or
   pip list
   ```

4. Verify system requirements:
   ```bash
   uname -a
   node --version
   npm --version
   ```

## Getting Help

If the above solutions don't resolve your issue:

1. Search our [issue tracker](https://github.com/user/project/issues)
2. Ask in our [community forum](https://community.example.com)
3. Contact [support](mailto:support@example.com) with:
   - Error logs
   - Configuration (redacted)
   - Steps to reproduce
   - Environment details
```

### 9.6.4 FAQ Page

```markdown
# Frequently Asked Questions

## General

### What is Product X?

Product X is a platform for building and deploying web applications
with zero configuration.

### Who is Product X for?

Developers, designers, and teams who want to rapidly build and deploy
web applications without managing infrastructure.

### Is Product X free?

We offer a free tier with 10 projects and 100MB storage. Paid plans
start at $10/month for additional features.

## Technical

### What languages are supported?

Node.js, Python, Go, Ruby, PHP, Rust, and Java.

### Can I use my own domain?

Yes! Custom domains are supported on all paid plans.

### How do I set up a custom domain?

1. Go to Settings > Domains
2. Enter your domain name
3. Add the DNS records shown
4. Wait for propagation (up to 48 hours)

## Billing

### How does pricing work?

You pay a monthly subscription based on your plan. Usage beyond plan
limits is billed per unit.

### Can I change my plan?

Yes, you can upgrade or downgrade at any time. Changes take effect
immediately.

### What payment methods do you accept?

Credit/debit cards (Visa, Mastercard, Amex) and PayPal.

## Account

### How do I reset my password?

Go to the login page and click "Forgot Password". Enter your email
address and we'll send reset instructions.

### How do I delete my account?

Go to Settings > Account > Delete Account. This action is irreversible
and will delete all your projects.

## Support

### How do I get help?

- Documentation: docs.product.com
- Community forum: community.product.com
- Email: support@product.com
- Chat: Available for paid plans
```

---

## 9.7 Product Documentation

### 9.7.1 Product Overviews

**Structure of a Compelling Product Overview:**

```
Product Overview
├── One-liner (10 words max)
├── Problem statement
├── Solution description
├── Key features (with icons)
├── Screenshots/GIFs
├── Use cases
├── Integrations
├── Pricing summary
└── Call to action
```

### 9.7.2 Feature Documentation Template

```markdown
# Feature Name

## Overview

1-2 paragraphs explaining what this feature does and why it matters.

## Prerequisites

- [ ] Product X account
- [ ] Admin permissions
- [ ] API key (if applicable)

## How It Works

```mermaid
sequenceDiagram
    participant User
    participant App
    participant API
    participant Database

    User->>App: Initiates feature
    App->>API: Sends request
    API->>Database: Validates & processes
    Database-->>API: Returns result
    API-->>App: Sends response
    App-->>User: Shows result
```

## Configuration

### Step 1: Enable the feature

```yaml
# config.yml
features:
  new_feature: true
```

### Step 2: Configure options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `rate` | number | 100 | Processing rate |
| `mode` | string | `auto` | Operating mode |
| `timeout` | number | 30000 | Timeout in ms |

## Usage

### Basic Usage

```javascript
const result = await client.features.process({
  input: 'data',
  mode: 'auto',
});
```

### Advanced Usage

```javascript
const result = await client.features.process({
  input: 'data',
  mode: 'custom',
  options: {
    priority: 'high',
    retry: true,
  },
});
```

## Best Practices

1. **Start with defaults** — Use default configuration first
2. **Monitor performance** — Check dashboards for bottlenecks
3. **Scale gradually** — Increase load incrementally
4. **Test in staging** — Always test in non-production first

## Limitations

- Maximum input size: 10MB
- Maximum processing time: 5 minutes
- Rate limit: 100 requests/second
```

### 9.7.3 Onboarding Guides

**30-Day Onboarding Sequence:**

| Day | Activity | Goal |
|-----|----------|------|
| 1 | Account setup | Create account, verify email |
| 2 | First project | Create first project |
| 3 | Basic configuration | Set up environment variables |
| 4 | Deploy first app | Run first deployment |
| 5 | Custom domain | Set up custom domain |
| 6 | Database setup | Connect database |
| 7 | API keys | Generate and use API keys |
| 8 | Monitoring | Set up monitoring dashboards |
| 9 | Team management | Invite team members |
| 10 | CI/CD setup | Configure deployment pipeline |
| 14 | Advanced features | Explore advanced features |
| 21 | Optimization | Performance tuning |
| 28 | Review | Best practices review |
| 30 | Graduation | Complete onboarding |

---

## 9.8 Knowledge Bases

### 9.8.1 Design Principles

| Principle | Description | Implementation |
|-----------|-------------|----------------|
| Findability | Users can locate information quickly | Search, navigation, cross-links |
| Scannability | Content is easy to skim | Headings, lists, bold for key terms |
| Accuracy | Information is current and correct | Review dates, version stamps |
| Completeness | All necessary information is present | Content audits, gap analysis |
| Consistency | Uniform structure and terminology | Style guides, templates |
| Accessibility | Content works for all users | Alt text, semantic HTML, contrast |
| Self-service | Users can solve problems without contact | Comprehensive troubleshooting |

### 9.8.2 Content Organization

```mermaid
graph TB
    subgraph "Knowledge Base Hierarchy"
        A[Category] --> B[Section]
        B --> C[Article]
        C --> D[Step]
        
        E[Tags]
        F[Related articles]
        G[See also links]
        
        C --> E
        C --> F
        C --> G
    end
    
    subgraph "Article Types"
        H[How-to]
        I[Troubleshooting]
        J[Concept]
        K[Reference]
        L[FAQ]
    end
```

### 9.8.3 Knowledge Base Software Comparison

| Feature | Confluence | Guru | Slab | GitBook |
|---------|-----------|------|------|---------|
| Markdown | Limited | Full | Full | Native |
| Versioning | Yes | Yes | Yes | Git-backed |
| Search | Good | Excellent | Good | Good |
| AI features | Atlas | AI Answers | AI | AI search |
| Permissions | Granular | Team-based | Channel-based | Space-based |
| Integrations | 800+ | 100+ | 50+ | 50+ |
| API | REST | GraphQL | REST | REST + Git |
| Price | $$$ | $$ | $$ | $ |
| Self-hosted | Yes (DC) | No | No | Yes |

---

## 9.9 Wiki Systems

### 9.9.1 MediaWiki with Markdown

MediaWiki (the software behind Wikipedia) can be extended with Markdown support:

```php
// LocalSettings.php - Enable Markdown extension
wfLoadExtension( 'Markdown' );

// Configuration
$wgMarkdownUseParserCache = true;
$wgMarkdownAllowHTML = false;
$wgMarkdownExtra = true;
```

**MediaWiki vs Modern Wiki Systems:**

| Aspect | MediaWiki | Wiki.js | GitBook |
|--------|-----------|---------|---------|
| Language | PHP | Node.js | Node.js |
| Database | MySQL | PostgreSQL | Git-backed |
| Markdown | Via extension | Native | Native |
| History | Built-in | Git-based | Git-based |
| Auth | Extension | Built-in | OAuth |
| API | Yes | GraphQL | REST |
| Performance | Moderate | Fast | Fast |
| Use case | Wikipedia-scale | Teams | Documentation |

### 9.9.2 Wiki.js

Wiki.js is a modern, open-source wiki built on Node.js:

```yaml
# docker-compose.yml for Wiki.js
version: '3.8'

services:
  db:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: wiki
      POSTGRES_PASSWORD: wikijsrocks
      POSTGRES_USER: wikijs
    volumes:
      - pgdata:/var/lib/postgresql/data
    restart: unless-stopped

  wiki:
    image: ghcr.io/requarks/wiki:2
    depends_on:
      - db
    environment:
      DB_TYPE: postgres
      DB_HOST: db
      DB_PORT: 5432
      DB_NAME: wiki
      DB_USER: wikijs
      DB_PASS: wikijsrocks
    ports:
      - "3000:3000"
    restart: unless-stopped

volumes:
  pgdata:
```

### 9.9.3 GitBook

GitBook is a Markdown-native documentation platform:

```
GitBook Architecture
├── Content
│   ├── Markdown files
│   ├── assets/
│   └── SUMMARY.md (navigation)
├── Integrations
│   ├── GitHub/GitLab sync
│   ├── Crowdin (translations)
│   └── Algolia (search)
├── Publishing
│   ├── gitbook.io (hosted)
│   ├── Custom domain
│   └── PDF/PDF export
└── AI features
    ├── AI search
    ├── Content suggestions
    └── Answer generation
```

---

## 9.10 Documentation Architecture

### 9.10.1 Information Architecture Principles

| Principle | Application |
|-----------|-------------|
| Hierarchy | Main navigation has 3-4 levels max |
| Consistency | Same patterns across all sections |
| Predictability | Users know where to find things |
| Context | Show related content and location |
| Flexibility | Multiple paths to same content |
| Scalability | Structure accommodates growth |

### 9.10.2 Content Modeling

```yaml
# content-model.yaml
content_types:
  - name: tutorial
    fields:
      - title: string
      - description: string
      - difficulty: enum[beginner, intermediate, advanced]
      - duration: string
      - prerequisites: array[reference]
      - steps: array[step]
      - related: array[reference]
  
  - name: api_reference
    fields:
      - endpoint: string
      - method: enum[GET, POST, PUT, DELETE, PATCH]
      - parameters: array[parameter]
      - responses: array[response]
      - examples: array[code_example]
  
  - name: troubleshooting
    fields:
      - symptom: string
      - cause: string
      - resolution: markdown
      - related_errors: array[string]

content_relationships:
  - tutorial → api_reference (prerequisites)
  - troubleshooting → concept (related)
  - concept → tutorial (further_reading)
```

### 9.10.3 Navigation Design

```mermaid
graph TD
    subgraph "Navigation Types"
        A[Primary Navigation] --> B[Sidebar]
        A --> C[Navbar]
        
        D[Secondary Navigation] --> E[Breadcrumbs]
        D --> F[Table of Contents]
        
        G[Tertiary Navigation] --> H[Cross-references]
        G --> I[Related Articles]
        G --> J[Next/Previous]
    end
    
    subgraph "Sidebar Patterns"
        K[Flat list]
        L[Hierarchical tree]
        M[Categorized groups]
        N[Filterable list]
    end
```

### 9.10.4 Search Design

**Search Ranking Factors:**

| Factor | Weight | Description |
|--------|--------|-------------|
| Title match | 40% | Exact or partial matches in title |
| Heading match | 25% | Matches in section headings |
| Content match | 20% | Matches in body text |
| Freshness | 10% | Newer content ranked higher |
| Popularity | 5% | Most-visited pages | 2.5% | Tags and metadata | 2.5% | Tags and metadata |

**Search Implementation Checklist:**

```
□ Full-text search engine
□ Faceted search (filter by category/version)
□ Search-as-you-type with suggestions
□ "Did you mean?" spelling corrections
□ Search result snippets with highlighted terms
□ Keyboard shortcuts (Cmd+K or /)
□ Search analytics tracking
□ Synonym handling
□ Boolean operators (AND, OR, NOT)
□ Advanced search syntax (site:, in:title:)
```

### 9.10.5 Content Reuse Strategies

| Strategy | Description | Example |
|----------|-------------|---------|
| Includes | Reuse file fragments | `{!include file.md}` |
| Variables | Reuse values | `{{product_name}}` |
| Templates | Reuse structures | `{% template %}` |
| Snippets | Reuse code blocks | Code block includes |
| Conditional | Platform-specific content | `{% if platform == 'mac' %}` |
| Transclusion | Embed content from other sources | `{!include api.md#section}` |

### 9.10.6 Single-Source Publishing

```mermaid
flowchart LR
    A[Single Source Content] --> B[Markdown files]
    
    B --> C[Web output]
    B --> D[PDF output]
    B --> E[E-book output]
    B --> F[API docs output]
    B --> G[In-app help]
    
    C --> H[HTML site]
    D --> I[PDF manual]
    E --> J[EPUB/MOBI]
    F --> K[Swagger UI]
    G --> L[Tooltips]
    
    style A fill:#4CAF50,color:white
```

---

## 9.11 Enterprise Documentation

### 9.11.1 Governance Models

| Role | Responsibility |
|------|----------------|
| Content Strategist | Overall content direction, taxonomy |
| Information Architect | Structure, navigation, search |
| Technical Writer | Create and maintain content |
| Subject Matter Expert | Technical accuracy review |
| Editor | Style, grammar, consistency |
| Approver | Final sign-off |
| Publisher | Deployment, release management |
| Analyst | Metrics, feedback, optimization |

### 9.11.2 Approval Workflows

```mermaid
flowchart LR
    A[Draft Created] --> B[Peer Review]
    B --> C[Technical Review]
    C --> D[Editorial Review]
    D --> E[Legal Review]
    E --> F[Final Approval]
    F --> G[Published]
    
    B -->|Changes needed| A
    C -->|Changes needed| A
    D -->|Changes needed| A
    E -->|Changes needed| A
```

### 9.11.3 Compliance Documentation

**SOC 2 Documentation Requirements:**

| Document | Purpose | Update Frequency |
|----------|---------|------------------|
| Security Policy | Overall security approach | Annually |
| Incident Response Plan | How incidents are handled | Quarterly |
| Business Continuity Plan | Disaster recovery | Annually |
| Data Classification Policy | Data handling | Annually |
| Access Control Policy | User access management | Annually |
| Change Management Policy | How changes are made | Annually |
| Vendor Management Policy | Third-party risk | Annually |
| Training Materials | Security awareness | Annually |

**ISO 27001 Documentation Structure:**

```
ISMS Documentation
├── Level 1: Policy
│   └── Information Security Policy
├── Level 2: Procedures
│   ├── Access Control Procedure
│   ├── Incident Management Procedure
│   └── Risk Assessment Procedure
├── Level 3: Work Instructions
│   ├── User Account Creation
│   ├── Log Review Process
│   └── Backup Verification
└── Level 4: Records
    ├── Access Logs
    ├── Training Records
    └── Audit Reports
```

### 9.11.4 Case Study: Microsoft Learn

Microsoft Learn is one of the largest documentation platforms:

```mermaid
graph TB
    subgraph "Content Types"
        A[Learn modules]
        B[Documentation]
        C[Tutorials]
        D[Sandbox environments]
        E[Certification paths]
    end
    
    subgraph "Platform"
        F[learn.microsoft.com]
        G[Profile & achievements]
        H[Progress tracking]
        I[Sandbox provisioning]
    end
    
    subgraph "Integration"
        J[Product docs]
        K[GitHub repos]
        L[Azure portal]
        M[VS Code]
        N[Microsoft Learn TV]
    end
    
    A --> F
    B --> F
    C --> F
    D --> H
    E --> G
    
    F --> J
    F --> K
    F --> L
    F --> M
    F --> N
```

**Key Metrics (2024):**

| Metric | Value |
|--------|-------|
| Modules | 5,000+ |
| Learning paths | 1,000+ |
| Languages | 25+ |
| Monthly active learners | 10M+ |
| Certifications | 50+ |
| Sandbox hours/month | 500K+ |

---

## 9.12 Documentation Lifecycle

```mermaid
flowchart TB
    subgraph "Documentation Lifecycle"
        A[Plan] --> B[Research]
        B --> C[Write]
        C --> D[Review]
        D --> E[Test]
        E --> F[Publish]
        F --> G[Maintain]
        G --> H[Analyze]
        H --> I{Retire?}
        I -->|No| B
        I -->|Yes| J[Archive]
    end
    
    subgraph "Each Phase"
        A --> A1["Define scope & audience"]
        A --> A2["Create outline"]
        B --> B1["User research"]
        B --> B2["Technical analysis"]
        C --> C1["Draft content"]
        C --> C2["Code examples"]
        D --> D1["Peer review"]
        D --> D2["Technical review"]
        E --> E1["Link check"]
        E --> E2["Build validation"]
        F --> F1["Deploy"]
        F --> F2["Announce"]
        G --> G1["Update"]
        G --> G2["Fix issues"]
        H --> H1["Analytics review"]
        H --> H2["User feedback"]
    end
```

**Lifecycle Phase Details:**

| Phase | Activities | Deliverables | Duration |
|-------|-----------|--------------|----------|
| Plan | Define scope, audience, goals, outline | Outline, brief | 1-2 days |
| Research | User research, competitive analysis, technical investigation | Research notes | 1-5 days |
| Write | Draft content, create code examples, add screenshots | Draft document | 2-10 days |
| Review | Peer review, tech review, editorial review | Review comments | 1-3 days |
| Test | Link checking, build testing, accessibility validation | Test results | 1 day |
| Publish | Deploy, announce, update sitemap | Published page | 1 day |
| Maintain | Update for releases, fix reported issues, improve | Updated content | Ongoing |
| Analyze | Track metrics, gather feedback, identify gaps | Analytics report | Monthly |
| Retire | Archive, redirect, remove from search | Archived content | Per decision |

---

## 9.13 Case Study: Documentation Systems Analysis

### 9.13.1 GitHub Docs

**URL:** docs.github.com
**SSG:** Jekyll (Ruby)
**Repository:** Public on GitHub

**Strong Points:**

| Aspect | Implementation |
|--------|---------------|
| Content structure | Organized by product area (GitHub.com, Actions, Packages, etc.) |
| Versioning | Feature-based (not product version) using Liquid conditionals |
| Search | Algolia DocSearch with version-aware results |
| Contribution | Direct "Edit this page" link to GitHub source |
| Localization | Crowdin with automated PRs for translations |
| API docs | Auto-generated from OpenAPI specs |
| Performance | CDN-cached static pages, <500ms load times |

**Architecture:**

```
docs.github.com
├── content/                  # Markdown source files
│   ├── github/              # GitHub.com docs
│   ├── actions/             # GitHub Actions docs
│   ├── packages/            # GitHub Packages docs
│   ├── issues/              # Issues & Projects docs
│   └── rest/                # REST API docs (auto-generated)
├── data/                    # YAML data files
│   ├── variables.yml        # Reusable variables
│   ├── reusables/           # Reusable content snippets
│   └── glossaries/          # Term definitions
├── includes/                # Liquid includes
├── assets/                  # CSS, JS, images
├── translations/            # Localized content
└── _config.yml              # Jekyll configuration
```

### 9.13.2 MDN Web Docs

**URL:** developer.mozilla.org
**SSG:** Custom (Yari)
**Repository:** Public on GitHub (openwebdocs)

**Strong Points:**

| Aspect | Implementation |
|--------|---------------|
| Breadth | Comprehensive web platform documentation |
| Examples | Interactive code examples with live editing |
| Browser compatibility | Auto-generated compatibility tables |
| Community | Open-source contributions via GitHub |
| Search | Built-in full-text search with filters |
| Interlinking | Extensive cross-references |
| Localization | 30+ languages via community |

**Content Types:**

| Type | Description | Examples |
|------|-------------|----------|
| Guides | Conceptual documentation | HTML basics, CSS layout |
| References | API documentation | Array.map(), Grid properties |
| Tutorials | Step-by-step learning | Learn web development |
| Glossary | Term definitions | "Event loop", "Hoisting" |
| Learn area | Structured curriculum | Front-end developer path |

### 9.13.3 Laravel Docs

**URL:** laravel.com/docs
**SSG:** Custom PHP

**Strong Points:**

| Aspect | Implementation |
|--------|---------------|
| Versioning | Prominent version switcher (5.x to 11.x) |
| Navigation | Categorized sidebar with search filtering |
| Content quality | Extremely well-written guides |
| Code examples | Every feature has runnable examples |
| Organization | Logical progression from setup to advanced |
| Search | Real-time sidebar search with highlighting |

```mermaid
graph LR
    subgraph "Laravel Documentation"
        A[Installation] --> B[Configuration]
        B --> C[Routing]
        C --> D[Middleware]
        D --> E[Controllers]
        E --> F[Requests]
        F --> G[Responses]
        
        H[Blade Templates]
        I[Eloquent ORM]
        J[Authentication]
        K[Testing]
        
        C --> H
        D --> J
        F --> I
        G --> K
    end
    
    subgraph "Learning Path"
        L1[Prologue] --> L2[Getting Started]
        L2 --> L3[Architecture]
        L3 --> L4[Basic Skills]
        L4 --> L5[Advanced Topics]
        L5 --> L6[Digging Deeper]
    end
```

**Cross-System Comparison:**

| Criteria | GitHub Docs | MDN Web Docs | Laravel Docs |
|----------|-------------|-------------|--------------|
| Content freshness | Real-time | PR-based | Release-based |
| Community contribution | Easy (edit link) | Moderate (fork) | Not directly |
| Search quality | Excellent | Good | Good |
| Versioning | Feature flags | URL-based | Dropdown |
| Code examples | Embedded | Interactive | Embedded |
| Localization | Professional | Community | Limited |
| Performance | Excellent | Good | Excellent |
| Mobile experience | Excellent | Excellent | Good |
| Accessibility | Excellent | Excellent | Good |

---

## 9.14 Exercises

### Exercise 1: README Audit

Review 5 popular open-source README files. Score each against the README best practices checklist. Write a report on what they do well and what could be improved.

### Exercise 2: Documentation System Architecture

Design a documentation system architecture for a SaaS product with 50 API endpoints, an SDK in 3 languages, and a user-facing dashboard. Include:

- Content model
- Build pipeline
- Deployment strategy
- Search implementation
- Versioning strategy

Create a Mermaid diagram of your architecture.

### Exercise 3: Migration Guide

Write a migration guide from a fictional v1 API to v2. Include:

- Breaking changes list
- Code examples for v1 → v2 migration
- Timeline
- Troubleshooting section

### Exercise 4: CONTRIBUTING.md

Write a CONTRIBUTING.md for an open-source project that:

- Has both code and documentation contributions
- Uses Conventional Commits
- Has a Code of Conduct
- Has a defined review process

### Exercise 5: Troubleshooting Guide

Write a troubleshooting guide for a database connection issue. Include:

- Common error codes
- Debug steps
- Resolution procedures
- Code snippets

### Exercise 6: Knowledge Base Setup

Configure a Wiki.js instance using Docker. Create:

- 3 categories
- 5 articles
- Navigation structure
- User permissions

### Exercise 7: API Documentation Page

Using OpenAPI 3.1, document an endpoint that:

- Creates a user
- Requires authentication
- Has validation rules
- Returns proper error responses

### Exercise 8: Documentation Workflow

Create a GitHub Actions workflow that:

- Builds documentation on PR
- Checks for broken links
- Lints Markdown
- Deploys to GitHub Pages on merge

### Exercise 9: Content Audit

Perform a content audit on an existing documentation site. Create:

- Inventory of all pages
- Quality assessment
- Gap analysis
- Improvement recommendations

### Exercise 10: Documentation Portal

Build a documentation portal using GitBook (or similar) that includes:

- Getting started guide
- API reference
- Troubleshooting guide
- Search functionality
- Version selector

---

## 9.15 Quiz

### Question 1

What is "Documentation as Code"?

A) Writing documentation in code comments only
B) Applying software development practices (version control, CI/CD, review) to documentation
C) Writing documentation in programming languages
D) Generating documentation automatically from code

<details>
<summary>Answer</summary>
**B.** Documentation as Code applies software development practices to documentation.
</details>

### Question 2

Which static site generator does GitHub Pages support natively without additional build configuration?

A) Docusaurus
B) Hugo
C) Jekyll
D) MkDocs

<details>
<summary>Answer</summary>
**C.** Jekyll is natively supported by GitHub Pages.
</details>

### Question 3

What is the purpose of the `CONTRIBUTING.md` file?

A) To list all contributors to a project
B) To explain how to contribute to a project, including coding standards and PR process
C) To contain the project's license
D) To serve as the main documentation page

<details>
<summary>Answer</summary>
**B.** CONTRIBUTING.md explains how to contribute to a project.
</details>

### Question 4

Which of the following is NOT a recommended section for a README?

A) Installation instructions
B) Quick start example
C) Detailed sales pitch
D) License information

<details>
<summary>Answer</summary>
**C.** Sales pitches are not appropriate for READMEs; they should focus on technical content.
</details>

### Question 5

What is the handbook-first approach used by GitLab?

A) Creating a physical handbook for new employees
B) Documenting all processes and decisions in a publicly accessible handbook before implementing them verbally
C) Writing documentation only in handbook format
D) Using handbooks instead of digital documentation

<details>
<summary>Answer</summary>
**B.** GitLab documents processes in their handbook before discussing them verbally.
</details>

### Question 6

Which search solution is specifically designed for documentation sites and requires no backend setup?

A) Elasticsearch
B) Algolia DocSearch
C) Meilisearch
D) Typesense

<details>
<summary>Answer</summary>
**B.** Algolia DocSearch is specifically designed for documentation sites.
</details>

### Question 7

What is the purpose of the `GOVERNANCE.md` file in open-source projects?

A) To list government regulations the project follows
B) To document how the project is governed, including decision-making processes and roles
C) To serve as the project's license agreement
D) To provide instructions for government contributors

<details>
<summary>Answer</summary>
**B.** GOVERNANCE.md documents the project's governance model.
</details>

### Question 8

Which of the following is a key principle of knowledge base design?

A) Make content as long as possible
B) Require users to contact support for common issues
C) Enable self-service so users can find answers without contacting support
D) Hide advanced content to keep things simple

<details>
<summary>Answer</summary>
**C.** Self-service is a key principle of knowledge base design.
</details>

### Question 9

What is the recommended maximum depth for navigation hierarchies in documentation?

A) 1-2 levels
B) 3-4 levels
C) 5-6 levels
D) No limit

<details>
<summary>Answer</summary>
**B.** 3-4 levels is recommended to prevent user disorientation.
</details>

### Question 10

What is the purpose of a documentation sprint?

A) A short race to write the most documentation
B) A focused event (1-5 days) where contributors work exclusively on documentation
C) A competition between documentation teams
D) An automated process that generates documentation

<details>
<summary>Answer</summary>
**B.** A documentation sprint is a focused event for documentation work.
</details>

### Question 11

Which of the following is an essential component of enterprise documentation governance?

A) Approval workflows with defined roles
B) Allowing anyone to publish directly
C) No review process
D) Single-person ownership

<details>
<summary>Answer</summary>
**A.** Approval workflows with defined roles are essential for enterprise governance.
</details>

### Question 12

What is the primary purpose of the documentation lifecycle's analysis phase?

A) To delete old documentation
B) To track metrics, gather feedback, and identify content gaps
C) To write new documentation
D) To review documentation for grammar

<details>
<summary>Answer</summary>
**B.** The analysis phase tracks metrics and identifies gaps.
</details>

### Question 13

Which HTTP status code indicates rate limiting in API documentation?

A) 400
B) 401
C) 429
D) 503

<details>
<summary>Answer</summary>
**C.** 429 Too Many Requests indicates rate limiting.
</details>

### Question 14

What is the primary difference between GitHub Wiki and a dedicated documentation site?

A) GitHub Wiki is faster
B) GitHub Wiki has limited customization and search compared to dedicated sites
C) GitHub Wiki supports versioning
D) There is no significant difference

<details>
<summary>Answer</summary>
**B.** GitHub Wiki has limited customization and search compared to dedicated sites.
</details>

### Question 15

What is single-source publishing?

A) Publishing from a single platform
B) Using one source of content to generate multiple output formats (web, PDF, e-book, etc.)
C) Having a single author for all documentation
D) Publishing to a single format

<details>
<summary>Answer</summary>
**B.** Single-source publishing creates multiple outputs from one source.
</details>

---

> **Next Module:** Module 10: MDX Masterclass — Learn how to combine Markdown with JSX for interactive documentation components.

---

*End of Module 9*
