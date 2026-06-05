# Module 13: Knowledge Management

Knowledge management (KM) is the practice of capturing, organizing, storing, and sharing knowledge within individuals and organizations. In the context of Markdown, KM leverages plain-text files, version control, and linking systems to create durable, maintainable knowledge bases.

---

## 13.1 Introduction to Knowledge Management

### What is Knowledge Management

Knowledge management is the systematic process of creating, sharing, using, and managing knowledge and information. It is a multidisciplinary approach that draws from cognitive science, information management, organizational behavior, and technology.

**Key concepts:**

- **Explicit knowledge**: Knowledge that can be easily articulated, documented, and shared (documents, procedures, code).
- **Tacit knowledge**: Knowledge that is difficult to express or transfer (experience, intuition, craft).
- **Implicit knowledge**: Knowledge that can be inferred from actions or context.

### Why Knowledge Management Matters

| Benefit | Impact |
|---------|--------|
| Reduced duplication | Teams don't rediscover the same solutions |
| Faster onboarding | New members learn from existing knowledge |
| Decision support | Historical context and documented decisions |
| Risk mitigation | Knowledge survives employee turnover |
| Continuous improvement | Lessons learned drive process enhancements |
| Organizational memory | Institutional knowledge is preserved |
| Collaboration | Shared knowledge enables better teamwork |

### Personal vs Organizational Knowledge Management

| Aspect | Personal KM | Organizational KM |
|--------|------------|------------------|
| Scope | Individual learning | Team/company knowledge |
| Tools | Obsidian, Notion, Roam | Confluence, SharePoint, Notion |
| Structure | Flexible, personalized | Standardized, governed |
| Goals | Learning, productivity, creativity | Efficiency, compliance, innovation |
| Maintenance | Self-managed | Dedicated roles |
| Privacy | Fully private | Access-controlled, audited |

### The Role of Markdown in KM

Markdown is the ideal format for knowledge management because:

- **Plain text**: Future-proof, no vendor lock-in
- **Version controllable**: Git-friendly diffs and history
- **Portable**: Works across every platform
- **Simple**: Low barrier to entry
- **Extensible**: Supports links, images, diagrams, metadata
- **Tool agnostic**: Many tools support it (Obsidian, VS Code, Logseq, etc.)

---

## 13.2 Second Brain Systems (Tiago Forte)

Tiago Forte's "Second Brain" methodology is one of the most influential modern knowledge management frameworks.

### The CODE Framework

CODE stands for **Capture, Organize, Distill, Express**.

#### Capture

The goal is to capture ideas, insights, and information without judgment.

**How to capture with Markdown:**

```markdown
# Inbox

## 2026-06-03
- Idea: The caching strategy in microservices could use write-behind pattern
- Quote: "The best time to plant a tree was 20 years ago. The second best time is now."
- Insight from meeting: GraphQL subscriptions for real-time updates
- Link to article: https://example.com/scalability-patterns
- Question: How does Redis Cluster handle network partitions?
```

**Capture tools for Markdown:**
- **Obsidian**: Quick note with Ctrl+N
- **VS Code**: Open a new scratch file
- **Drafts** (macOS/iOS): Quick capture to Markdown
- **Simplenote**: Cross-platform Markdown notes
- **GitHub Gist**: Quick Markdown snippets

#### Organize

The PARA method organizes captured information into actionable categories.

```markdown
# Projects (Active, time-bound outcomes)
- [ ] Launch new API documentation
- [ ] Migrate legacy wiki to Markdown

# Areas (Ongoing responsibilities)
- Performance optimization
- Technical writing standards
- Team mentoring

# Resources (Topics of interest)
- Knowledge management research
- Diagram-as-code tools
- System design patterns

# Archives (Inactive items)
- 2025 Annual report
- Legacy onboarding docs
- Old project X
```

#### Distill

Progressive summarization extracts the most valuable information from captured content.

**Levels of distillation:**

```
Level 1: Original capture (full text)
Level 2: Bold passages (what stands out)
Level 3: Highlighted passages (most important)
Level 4: Executive summary (1-2 sentences)
Level 5: Remix (original insight, new connections)
```

**Example of progressive summarization in Markdown:**

```markdown
# Article: Microservices Data Patterns

## Level 4 Summary (Executive Summary)
The Saga pattern enables distributed transactions across microservices without two-phase commit, using compensating actions for rollback.

## Level 3 (Key Insights)
**Saga pattern**: Each local transaction publishes an event that triggers the next step. If a step fails, compensating transactions undo the previous steps.
- **Orchestration saga**: A central coordinator tells participants what to do
- **Choreography saga**: Each participant publishes events that others consume

## Level 2 (Bold)
**Saga pattern**: Each local transaction publishes an event that triggers the next step.
**Countermeasure**: Implement idempotency keys to handle duplicate events.
**Monitoring**: Use Jaeger for distributed tracing of saga flows.

## Level 1 (Full original)
[Full article text captured here]
```

#### Express

Express is about sharing your knowledge — writing, presenting, or building something with what you've learned.

```markdown
# Expression: Microservices Data Patterns Guide

## From My Second Brain
Based on captured research, distilled insights, and personal experience:

## Key Recommendation
Use **orchestration saga** for complex workflows (e.g., order fulfillment) and **choreography saga** for simpler, event-driven flows.

## Practical Implementation
[Code examples, configuration snippets, deployment steps]
```

### PARA Method

PARA stands for **Projects, Areas, Resources, Archives**. It is the organizational backbone of the Second Brain.

| Category | Definition | Example |
|----------|------------|---------|
| **Projects** | Short-term outcomes with deadlines | "Launch v2 API", "Write monthly report" |
| **Areas** | Long-term responsibilities | "Health", "Career", "Finances" |
| **Resources** | Topics of ongoing interest | "Machine learning", "Gardening" |
| **Archives** | Inactive items from above three | "Old projects", "Past roles" |

#### Folder Structure for PARA in Markdown

```
~/second-brain/
├── 1-Projects/
│   ├── active/
│   │   ├── api-v2-launch/
│   │   │   ├── index.md
│   │   │   ├── tasks.md
│   │   │   └── meeting-notes.md
│   │   └── obsidian-migration/
│   │       └── index.md
│   └── completed/
│       └── 2025-annual-report.md
├── 2-Areas/
│   ├── career.md
│   ├── health.md
│   ├── finance.md
│   └── technical-writing/
│       ├── index.md
│       ├── style-guide.md
│       └── tools-overview.md
├── 3-Resources/
│   ├── knowledge-management.md
│   ├── system-design/
│   │   ├── caching.md
│   │   ├── databases.md
│   │   └── message-queues.md
│   └── programming/
│       ├── go.md
│       ├── python.md
│       └── typescript.md
└── 4-Archives/
    ├── projects-2024/
    ├── old-blog-ideas/
    └── previous-job-notes/
```

#### PARA in Obsidian

In Obsidian, PARA can be implemented with folders and tags:

```
PARA Vault/
├── Projects/
├── Areas/
├── Resources/
└── Archives/
```

Or you can use tags:

```markdown
#project/api-v2
#area/health
#resource/machine-learning
#archive/2024
```

### Progressive Summarization

Progressive summarization is the technique of distilling captured information into increasingly valuable layers.

**The 5 Levels:**

1. **Original capture** — Save the entire article, video transcript, or notes
2. **Bold passages** — On review, bold the parts that stand out
3. **Highlighted passages** — Mark the best of the bold (most important 10-20%)
4. **Executive summary** — Write a 1-3 sentence summary in your own words
5. **Remix** — Create something new by combining multiple sources

**Markdown syntax for distillation:**

```markdown
# Original
Here is the full content of the article...

**Bold**: This is a key insight from the article...

==Highlighted==: This is the most important insight...

## My Summary
The article explains that distributed tracing needs three pillars: instrumentation, collection, and visualization. OpenTelemetry provides the standard.
```

### Implementing Second Brain with Markdown

**Step 1: Choose your tools**
- **Editor**: Obsidian, VS Code, Logseq
- **Sync**: Git, Obsidian Sync, Dropbox
- **Capture**: Mobile app, browser extension, quick note system

**Step 2: Set up your folder structure**

```
~/brain/
├── inbox/           # Everything captured here first
├── projects/        # Active projects
├── notes/           # Permanent notes
├── references/      # External references
└── archive/         # Everything else
```

**Step 3: Create a daily note template**

```markdown
# {{date:YYYY-MM-DD}} {{day:dddd}}

## Today's Focus

## Captures

## Tasks
- [ ]

## Meetings

## Notes

## Gratitude
-
```

**Step 4: Link everything**

```markdown
How [[Distributed Tracing]] works with [[OpenTelemetry]] and [[Jaeger]].

This connects to our [[Saga Pattern]] implementation from last sprint.
```

**Step 5: Review weekly**

```markdown
# Weekly Review: {{date:YYYY-WW}}

## Project Progress

## Inbox Zero
- Items to archive:
- Items to process:

## Areas Check
- Career:
- Health:
- Learning:

## Next Week Intentions
-
```

---

## 13.3 PKM (Personal Knowledge Management)

### What is PKM

Personal Knowledge Management (PKM) is the practice of taking responsibility for what you learn, how you organize it, and how you apply it. Unlike organizational KM, PKM is self-directed and personalized.

**Core principles:**
1. **You own your knowledge**: Don't rely on organizational systems
2. **Connect ideas**: Knowledge grows through connections
3. **Express to understand**: Writing is thinking
4. **Build iteratively**: Start simple, evolve over time
5. **Make it frictionless**: Capture should be effortless

### Building a Personal Knowledge Base

A PKM system in Markdown typically includes:

**Types of notes:**

| Note Type | Purpose | Example |
|-----------|---------|---------|
| **Fleeting notes** | Quick capture | Ideas, quotes, reminders |
| **Literature notes** | External sources | Book notes, article summaries |
| **Permanent notes** | Your own thinking | Concepts, principles, insights |
| **Map of Content (MOC)** | Navigation | Overview of a topic area |
| **Daily notes** | Day-to-day logging | Work log, journal |
| **Project notes** | Project-specific | Meeting notes, decisions |
| **Reference notes** | Technical reference | API docs, configuration |

### Note-Taking Workflows

#### The 5-Step Workflow

1. **Capture** — Write down anything interesting immediately
2. **Clarify** — Process captures into clear notes
3. **Organize** — File notes in appropriate places, add links
4. **Connect** — Add bi-directional links between related concepts
5. **Create** — Use your notes to produce new work

#### The Daily Workflow

```markdown
# Morning (5 minutes)
1. Review yesterday's notes
2. Set today's intention
3. Check project status

# Throughout the day
- Capture continuously
- Link related ideas immediately

# Evening (10 minutes)
1. Process inbox
2. Update project notes
3. Connect today's captures to existing knowledge
4. Set up tomorrow

# Weekly (30 minutes)
1. Inbox zero
2. Review all areas
3. Archive completed projects
4. Update maps of content
```

### Connecting Ideas

Bi-directional linking is the most powerful feature of modern PKM tools.

**In Markdown:**

```markdown
# Distributed Systems

Key concepts:
- [[Consistency Models]]
- [[CAP Theorem]]
- [[Raft Consensus Algorithm]]
- [[Distributed Tracing]]

Related areas:
- [[Database Replication]]
- [[Message Queues]]
- [[Microservices Architecture]]

## Overview
Distributed systems are collections of independent computers that appear as a single coherent system to the user.

See also: [[Fallacies of Distributed Computing]]
```

**Tools that support bi-directional linking:**
- Obsidian (native with [[wiki-links]])
- Logseq (native [[links]])
- Roam Research (native [[links]])
- VS Code with Foam extension
- Dendron

### Retrieval Practice

Retrieval practice — actively recalling information — is one of the most effective learning techniques.

**Active recall prompts in Markdown:**

```markdown
# Retrieval Questions: Distributed Systems

## Q: What is the CAP theorem?
A: Consistency, Availability, Partition tolerance — you can have at most two of three in a distributed system.

## Q: How does a Raft leader election work?
A:
- Nodes are in follower, candidate, or leader state
- Leaders send heartbeats
- Candidates request votes on timeout
- Majority vote wins

## Q: What is the difference between orchestration and choreography sagas?
A: Orchestration uses a central coordinator; choreography uses event-driven peer-to-peer communication.
```

### Spaced Repetition Integration

Spaced repetition systems (SRS) like Anki can integrate with Markdown PKM systems.

**Exporting notes to Anki:**

Use tools like Obsidian Anki plugin, or manually create CSV for import:

```csv
Question,Answer
"What is CAP theorem?","Consistency, Availability, Partition tolerance — pick two"
"What is idempotency?","An operation that produces the same result regardless of how many times it is executed"
```

**Anki-flavored Markdown (Obsidian plugin):**

```markdown
## What is the CAP theorem?
?
Consistency, Availability, Partition tolerance — pick any two.

## What is idempotency?
?
An operation that produces the same result regardless of how many times it is executed.
```

---

## 13.4 Digital Gardens

### What is a Digital Garden

A digital garden is a collection of publicly available notes, writings, and ideas that grow organically over time. Unlike traditional blogs (which publish finished articles) or wikis (which aim for completeness), digital gardens are:

- **In perpetual growth**: Ideas are never "finished"
- **Wiki-style linked**: Notes connect to each other
- **Time-visible**: Readers see the evolution of ideas
- **Non-linear**: No chronological ordering
- **Personal**: Reflects the author's thinking

### Digital Garden vs Blog

| Aspect | Blog | Digital Garden |
|--------|------|---------------|
| Structure | Chronological, reverse order | Networked, linked |
| Content | Polished, finished | Growing, evolving |
| Reading | Linear, start to finish | Exploratory, follow links |
| Publishing | Publish when done | Publish when started |
| Updates | Rarely updated after publish | Continuously updated |
| Tone | Authoritative | Personal, curious |
| Notes | Not included | Core component |

### Growing Ideas Publicly

Digital gardens follow a growth lifecycle for each note:

```
Seedling -> Growing -> Evergreen
```

**In practice:**

```markdown
# Status: Seedling

Just captured an idea about how event sourcing could simplify audit logging.
Need to research more and connect to existing notes.

# Status: Growing

Event sourcing stores state changes as a sequence of events. This provides:
- Complete audit trail
- Time travel (reconstruct past state)
- Event-driven architecture compatibility

Related: [[CQRS]], [[Saga Pattern]], [[Event-Driven Architecture]]

# Status: Evergreen

## Event Sourcing
Event sourcing persists the state of a business entity as a sequence of state-changing events.

### Key Characteristics
- Events are immutable facts
- Current state is derived by replaying events
- Event store is the source of truth
- Read models can be built from the event stream

### When to Use
- Systems requiring complete audit trails
- Complex domain logic with state transitions
- Integration with event-driven architectures

### When Not to Use
- Simple CRUD applications
- Systems where current state is all that matters
- When storage cost of events is prohibitive

### See Also
[[CQRS]], [[Event-Driven Architecture]], [[Domain-Driven Design]]
```

### Wiki-Style Linking

Digital gardens use wiki-style links extensively:

```markdown
# [[Distributed Tracing]]

[[OpenTelemetry]] provides the standard for [[Distributed Tracing]] in modern applications.

Key components:
- [[Trace]] — end-to-end request path
- [[Span]] — a single unit of work
- [[Span Context]] — propagation metadata

Tools: [[Jaeger]], [[Zipkin]], [[Tempo]]

Related: [[Observability]], [[Monitoring]], [[Logging]]
```

### Bi-Directional Links

Bi-directional links show not just where a link goes, but what links to it:

```markdown
# Distributed Tracing

Backlinks:
- [[Observability]] references distributed tracing
- [[OpenTelemetry]] implemented in OpenTelemetry
- [[Microservices Monitoring]] key monitoring technique
- [[SRE Practices]] recommended by SRE
```

Tools like Obsidian display backlinks automatically. For static sites, use plugins to render them.

### Digital Garden Examples

Notable digital gardens:
- **Andy Matuschak's notes** (notes.andymatuschak.org) — Pioneer of digital gardening
- **Gwern's website** (gwern.net) — Long-form research notes
- **Tom Critchlow** (tomcritchlow.com) — Small pieces loosely joined
- **Maggie Appleton** (maggieappleton.com/garden) — Visual digital garden
- **Anne-Laure Le Cunff** (nesslabs.com/mind-garden) — Mind garden concept
- **Joel Hooks** (joelhooks.com/digital-garden) — Developer-focused garden

### Digital Garden Tools

#### Obsidian Publish

Obsidian Publish turns an Obsidian vault into a website:
- Markdown-native
- Bi-directional links
- Graph view
- Customizable themes
- Sidebar navigation
- Custom domains

**Pricing**: Paid subscription ($10/month)

#### Quartz (Digital Garden with Markdown)

Quartz is a static site generator specifically for digital gardens:

```bash
# Quick start
npx quartz create my-garden
cd my-garden
npx quartz build
npx quartz serve
```

Features:
- Bi-directional links
- Graph view
- Full-text search
- Dark/light mode
- Obsidian vault compatible
- Deploy to GitHub Pages, Netlify, Vercel

**Content structure:**

```
content/
├── index.md
├── concepts/
│   ├── distributed-tracing.md
│   ├── event-sourcing.md
│   └── cqrs.md
├── tools/
│   ├── obsidian.md
│   └── d2lang.md
├── projects/
│   └── knowledge-base-migration.md
└── about.md
```

#### Gatsby Digital Garden

Use the Gatsby theme for digital gardens:

```bash
npm init gatsby -- -y my-garden
cd my-garden
npm install gatsby-theme-garden
```

Configure in gatsby-config.js:

```javascript
module.exports = {
    plugins: [
        {
            resolve: 'gatsby-theme-garden',
            options: {
                contentPath: 'content/garden',
                basePath: '/garden',
            },
        },
    ],
};
```

#### GitHub Pages as Garden

A minimal digital garden can be hosted for free on GitHub Pages:

```bash
git clone https://github.com/username/username.github.io
cd username.github.io

# Use Jekyll or a static site generator
```

Supported tools for GitHub Pages gardens:
- **Jekyll** (native GitHub Pages support)
- **Hugo** (fast, flexible)
- **11ty** (JavaScript-based)
- **Quartz** (digital garden focused)

#### Flowershow

Flowershow is a platform specifically for publishing Markdown-based digital gardens:

```bash
npm create flowershow@latest
cd my-garden
npm run dev
```

Features:
- Built-in bi-directional links
- Automatic backlinks
- Graph visualization
- Search
- GitHub sync
- Free hosting option

### Building a Digital Garden Step by Step

**Step 1: Choose your foundation**

```bash
npx quartz create my-garden
cd my-garden
```

Create your folder structure:

```
garden/
├── content/
│   ├── inbox/
│   ├── seedlings/
│   ├── growing/
│   ├── evergreen/
│   ├── maps-of-content/
│   └── about.md
└── quartz.config.ts
```

**Step 2: Create your first pages**

```markdown
---
title: Welcome to My Digital Garden
---

# My Digital Garden

I'm growing my thoughts here in public. Nothing is finished, everything is evolving.

## Maps of Content
- [[Distributed Systems]]
- [[Knowledge Management]]
- [[Programming Languages]]

## Recently Planted
- [[Event Sourcing]] — seedling
- [[Second Brain Method]] — growing
- [[Markdown Best Practices]] — evergreen
```

```markdown
---
title: Knowledge Management
tags: [km, productivity, learning]
status: growing
date: 2026-06-03
---

# Knowledge Management

Knowledge management is the practice of capturing, organizing, and sharing knowledge.

## Related
- [[Second Brain]] — the CODE framework
- [[Zettelkasten]] — atomic notes method
- [[PKM Tools]] — tool comparison
```

**Step 3: Build and deploy**

```bash
# Build
npx quartz build

# Deploy to GitHub Pages
npx quartz sync
```

---

## 13.5 Zettelkasten Method (Niklas Luhmann)

The Zettelkasten (German for "slip box" or "note box") is a note-taking method developed by Niklas Luhmann, a German sociologist who produced over 70 books and 400 scholarly articles from his system of 90,000 index cards.

### What is Zettelkasten

Luhmann's Zettelkasten contained approximately 90,000 hand-written index cards with notes, references, and connections. The system's power comes not from the individual notes but from the network of connections between them.

**Core principles:**
1. **Atomicity**: Each note contains exactly one idea
2. **Autonomy**: Each note is self-contained and understandable on its own
3. **Connection**: Notes are linked to each other
4. **Growth**: The system becomes more valuable as it grows

### Atomic Notes

An atomic note contains exactly one idea — no more, no less.

**Example of an atomic note:**

```markdown
---
id: 202606031452
title: CAP Theorem Trade-offs
created: 2026-06-03
tags: [distributed-systems, cap-theorem, database]
---

# CAP Theorem Trade-offs

In distributed systems, you can only guarantee two of three properties:
- **Consistency** (every read gets the latest write)
- **Availability** (every request gets a non-error response)
- **Partition Tolerance** (system continues despite network failures)

## Practical implications
- **CP systems** (e.g., HBase, MongoDB): sacrifice availability during partitions
- **AP systems** (e.g., Cassandra, DynamoDB): sacrifice consistency
- **CA systems**: not possible in distributed environments

## References
Brewer, E. (2000). "Towards Robust Distributed Systems" (PODC keynote)

## Linked to
- [[202606031453]] Eventual Consistency
- [[202606031454]] PACELC Theorem
```

### Connection Notes

Connection notes (also known as bridge notes) link multiple ideas together:

```markdown
---
id: 202606031500
title: Link: CAP -> Consistency Models
created: 2026-06-03
---

# CAP -> Consistency Models

How CAP theorem relates to consistency models:

| CAP choice | Consistency Model | Example |
|------------|------------------|---------|
| CP | Linearizability | Spanner, etcd |
| AP | Eventual Consistency | DynamoDB, Cassandra |

## Insight
The choice of consistency model in CAP is a spectrum, not binary.

## Connected notes
- [[202606031452]] CAP Theorem Trade-offs
- [[202606031453]] Eventual Consistency
- [[202606031501]] Linearizability

## Question
Could a system dynamically switch between CP and AP based on network conditions?
```

### Sequence Notes

Sequence notes create ordered chains of reasoning:

```markdown
---
id: 202606031510
title: Sequence: Distributed Data Systems
created: 2026-06-03
---

# Understanding Distributed Data Systems (Sequence)

1. [[202606031452]] CAP Theorem Trade-offs
2. [[202606031453]] Eventual Consistency
3. [[202606031501]] Linearizability
4. [[202606031502]] Conflict-free Replicated Data Types (CRDTs)
5. [[202606031503]] Distributed Transactions (2PC, Saga)

## Why this sequence
Each concept builds on the previous one. Start with CAP to understand the fundamental constraint, then explore consistency models, and finally look at practical solutions.
```

### Structure Notes

Structure notes (also called maps of content) provide an overview of a topic area:

```markdown
---
id: 202606031520
title: Structure: Distributed Systems
created: 2026-06-03
---

# Distributed Systems

## Fundamental Concepts
- [[202606031452]] CAP Theorem
- [[202606031453]] Consistency Models
- [[202606031521]] Fallacies of Distributed Computing

## Architecture Patterns
- [[202606031522]] Microservices
- [[202606031523]] Event-Driven Architecture
- [[202606031524]] CQRS

## Data Management
- [[202606031525]] Database Sharding
- [[202606031526]] Replication Strategies
- [[202606031527]] Distributed Transactions

## Observability
- [[202606031528]] Distributed Tracing
- [[202606031529]] Metrics and Monitoring
- [[202606031530]] Logging
```

### Implementing Zettelkasten in Markdown

**Filing system options:**

#### Option 1: Date-based IDs

```
notes/
├── 202606031452-cap-theorem.md
├── 202606031453-eventual-consistency.md
├── 202606031500-link-cap-consistency.md
```

#### Option 2: Content-based directories

```
notes/
├── distributed-systems/
│   ├── cap-theorem.md
│   ├── consistency-models.md
│   └── pacelc-theorem.md
├── knowledge-management/
│   ├── zettelkasten.md
│   ├── second-brain.md
│   └── digital-gardens.md
└── programming/
    ├── go-concurrency.md
    └── type-systems.md
```

#### Option 3: Sequential IDs (Luhmann's method)

```
notes/
├── 1.md (Welcome/Index)
├── 1a.md
├── 1a1.md
├── 1a2.md
├── 1b.md
├── 2.md
```

### Slip Box Principles

Luhmann's original principles:

1. **Each note is an atomic unit** — one idea, one note
2. **Notes are connected by links** — no isolated notes
3. **Links show relationships** — not just "see also" but why they relate
4. **Notes have unique identifiers** — find any note precisely
5. **Notes are written for yourself** — you must understand them years later
6. **The system is open-ended** — never "complete"
7. **Structure emerges from bottom up** — not top-down categorization

### Practical Zettelkasten Workflow

**Daily practice:**

1. **Capture** fleeting notes into inbox
2. **Process** inbox daily:
   - Turn fleeting notes into atomic permanent notes
   - Give each note a unique ID
   - Write in your own words
   - Add links to existing notes
   - Add bibliographic references
3. **Connect**:
   - Check what notes link to concepts in your new note
   - Add the new note to relevant structure notes
4. **Review**:
   - Weekly: Review new notes, add missing links
   - Monthly: Review structure notes for gaps
   - Quarterly: Identify clusters and themes

---

## 13.6 Obsidian Deep Dive

### What is Obsidian

Obsidian is a Markdown-based knowledge management application. It runs locally, stores notes as plain Markdown files, and provides extensive features for linking, visualizing, and organizing knowledge.

**Key philosophy:**
- Your data is plain text Markdown files
- You own your data completely
- Extensibility via community plugins
- Privacy by default (local-first)

### Markdown-Native Knowledge Management

Obsidian uses standard Markdown with some extensions:

**Wiki links:**
```markdown
[[Note Name]]
[[Note Name|Display Text]]
[[Note Name#Section]]
[[Note Name#^block-id]]
```

**Embedded files:**
```markdown
![[Other Note]]
![[image.png]]
![[document.pdf]]
```

**Metadata (frontmatter):**
```markdown
---
title: Distributed Tracing
tags: [observability, distributed-systems]
created: 2026-06-01
updated: 2026-06-03
status: growing
aliases: [trace analysis, request tracing]
---
```

### Graph View

The graph view visualizes connections between notes:

- **Nodes**: Each note is a node
- **Edges**: Links between notes
- **Colors**: Color groups by tag, folder, or search
- **Filters**: Show/hide based on search criteria
- **Local graph**: Graph for a single note and its connections

**Uses of graph view:**
- Identify clusters of related topics
- Find orphan notes (no connections)
- Discover unexpected connections
- Navigate your knowledge base visually
- Presentation mode for showing relationships

### Backlinks

Backlinks are automatically tracked and displayed:

```markdown
# Distributed Tracing

## Backlinks
- [[Observability Strategy]] mentions distributed tracing
- [[OpenTelemetry Setup]] includes tracing configuration
- [[Performance Debugging]] uses traces for root cause analysis
- [[SRE Runbook]] references tracing in incident response

## Outgoing Links
- [[OpenTelemetry]]
- [[Jaeger]]
- [[Span]]
- [[Trace Context]]
```

### Tags

Tags in Obsidian are searchable and appear in the tag pane:

```markdown
#tag-name
#nested/tag/name

---
tags: [distributed-systems, observability, aws]
---
```

**Tag best practices:**
- Use tags for orthogonal categories (not hierarchical)
- Combine tags with folders
- Use nested tags sparingly
- Keep tag count under 50 for usability
- Review and consolidate tags quarterly

### Canvas

Obsidian Canvas is a visual tool for arranging notes, images, and embeddings on an infinite canvas.

**Use cases:**
- Mind mapping
- Project planning
- Architecture sketching
- Concept mapping
- Presentation layout

**Canvas content can include:**
- Note cards
- Images
- Videos
- PDFs
- Embedded queries
- Custom text and drawings

### Community Plugins

Obsidian has a rich plugin ecosystem. Here are the most impactful ones for knowledge management:

#### Dataview (Query Notes)

Dataview turns your vault into a queryable database:

```markdown
## All Notes about Distributed Systems
```dataview
TABLE file.ctime as "Created", status as "Status"
FROM #distributed-systems
SORT file.ctime DESC
```

```dataview
TASK
FROM #project/api-v2
WHERE !completed
GROUP BY file.link
```

```dataview
CALENDAR file.ctime
FROM #daily-note
```

#### Excalidraw (Diagrams)

The Excalidraw plugin brings hand-drawn diagrams into Obsidian:

```markdown
![[architecture-sketch.excalidraw]]
```

Features:
- Embed drawings in notes
- Bi-directional linking from drawings
- Automatic export to PNG/SVG
- Drawing scripts for automation
- Integrated with Obsidian theming

#### Kanban (Project Management)

Turn Markdown files into Kanban boards:

```markdown
---

## To Do
- [ ] Set up CI/CD pipeline
- [ ] Write API documentation

## In Progress
- [ ] Implement user authentication

## Done
- [x] Project kickoff meeting
- [x] Repository setup
```

#### Calendar (Daily Notes)

Calendar plugin for managing daily notes:
- Visual calendar view
- Click to create/open daily notes
- Weekly and monthly views
- Integration with templates

#### Templates

Template plugin for consistent note creation:

```markdown
---
title: "{{title}}"
created: {{date}}
tags: []
---

# {{title}}

## Overview

## Key Points

## Questions

## Related
-
```

#### Git Integration

Obsidian Git plugin provides automatic version control:

```markdown
# In Obsidian settings
- Auto-commit interval: 10 minutes
- Auto-push: on
- Pull on startup: yes
```

**Benefits:**
- Version history for every note
- Sync across devices via GitHub
- Backup and disaster recovery
- Collaboration via Git
- CI/CD for published content

### Obsidian Publish

Obsidian Publish converts your vault to a website:

**Features:**
- Full-text search
- Graph view
- Bi-directional links
- Custom CSS theming
- Sidebar navigation
- Password protection
- Custom domain support

**Pricing:** $10/month

**Setup:**
1. Settings -> Obsidian Publish -> Sign in
2. Select notes to publish
3. Configure navigation
4. Deploy with one click

### Obsidian Sync

Obsidian Sync is an encrypted sync service:

**Features:**
- End-to-end encryption
- Version history (1 year)
- Cross-device sync
- Selective folder sync
- No file size limits

**Pricing:** $5/month

### Building a Knowledge Management System in Obsidian

#### Step 1: Create Your Folder Structure

```
vault/
├── 00-Meta/
│   ├── Templates/
│   ├── Attachments/
│   └── Scripts/
├── 01-Inbox/
├── 02-Projects/
├── 03-Areas/
├── 04-Resources/
├── 05-Permanent-Notes/
├── 06-Daily-Notes/
├── 07-Maps-of-Content/
├── 08-Archive/
└── 09-Dataview-Queries/
```

#### Step 2: Install Essential Plugins

1. **Dataview** — query your notes
2. **Templater** — advanced templates
3. **Calendar** — daily note navigation
4. **Obsidian Git** — version control
5. **Excalidraw** — visual thinking
6. **Kanban** — project management
7. **Tag Wrangler** — tag management
8. **Note Refactor** — split notes
9. **Sliding Panes** — multi-pane view
10. **Minimal Theme Settings** — theming

#### Step 3: Set Up Templates

**Project note template:**

```markdown
---
created: {{date:YYYY-MM-DD}}
status: active
type: project
due:
---

# {{title}}

## Objective

## Tasks
- [ ]

## Timeline
- Start:
- Milestone 1:
- Due:

## Resources
-

## Related Notes
-
```

**Permanent note template:**

```markdown
---
created: {{date:YYYY-MM-DD}}
tags: []
aliases: []
status: seedling
---

# {{title}}

## Definition

## Key Insights

## Examples

## Counter-Examples

## References

## Connected To
-
```

#### Step 4: Create Daily Workflow

**Daily note template:**

```markdown
---
created: {{date:YYYY-MM-DD}}
type: daily
---

# {{date:YYYY-MM-DD}}

## Today's Focus

## Captures

## Tasks
- [ ]

## Log

## Gratitude
-

## Tomorrow
-
```

**Workflow:**
1. Open daily note each morning
2. Capture ideas throughout the day into Inbox
3. Process Inbox in the evening (turn into permanent notes)
4. Link new notes to existing ones
5. Update Maps of Content

#### Step 5: Review Process

**Weekly review:**
- Go through Inbox (empty it)
- Review weekly notes
- Update project status
- Add missing links

**Monthly review:**
- Review Maps of Content
- Consolidate related notes
- Archive completed projects
- Update tags

**Quarterly review:**
- Audit entire vault structure
- Remove orphan notes
- Merge duplicate notes
- Update outdated information

### Complete Example Vault Structure

```
knowledge-vault/
├── 00-Meta/
│   ├── Templates/
│   │   ├── daily-note.md
│   │   ├── permanent-note.md
│   │   ├── project-note.md
│   │   ├── literature-note.md
│   │   └── moc-template.md
│   ├── Scripts/
│   │   └── weekly-review.js
│   └── Attachments/
│       ├── diagrams/
│       └── images/
├── 01-Inbox/
│   ├── idea-1.md
│   ├── interesting-article.md
│   └── meeting-notes-todo.md
├── 02-Projects/
│   ├── active/
│   │   ├── knowledge-base-migration.md
│   │   └── api-documentation-v2.md
│   └── completed/
│       └── legacy-wiki-audit.md
├── 03-Areas/
│   ├── career.md
│   ├── health.md
│   ├── finance.md
│   └── learning.md
├── 04-Resources/
│   ├── distributed-systems/
│   │   ├── cap-theorem.md
│   │   ├── raft-consensus.md
│   │   └── distributed-tracing.md
│   ├── knowledge-management/
│   │   ├── zettelkasten.md
│   │   ├── second-brain.md
│   │   └── digital-gardens.md
│   └── programming/
│       ├── go.md
│       ├── typescript.md
│       └── python.md
├── 05-Permanent-Notes/
│   ├── 202606031452-cap-theorem.md
│   ├── 202606031453-eventual-consistency.md
│   ├── 202606031500-link-cap-consistency.md
│   └── 202606031520-structure-distributed-systems.md
├── 06-Daily-Notes/
│   ├── 2026-06-01.md
│   ├── 2026-06-02.md
│   └── 2026-06-03.md
├── 07-Maps-of-Content/
│   ├── distributed-systems-moc.md
│   ├── knowledge-management-moc.md
│   └── programming-moc.md
└── 08-Archive/
    ├── old-projects/
    └── past-learning/
```

---

## 13.7 Knowledge Graphs

### What are Knowledge Graphs

A knowledge graph is a structured representation of knowledge as a network of entities and their relationships. Nodes represent entities (concepts, people, documents), and edges represent relationships.

**Components:**
- **Nodes**: Entities (notes, concepts, people, projects)
- **Edges**: Relationships (links to, depends on, authored by)
- **Properties**: Attributes of nodes and edges

### Nodes, Edges, Properties

**In Markdown knowledge management:**

```yaml
# Node: Note with properties
---
id: distributed-tracing
type: concept
domain: distributed-systems
difficulty: intermediate
status: growing
created: 2026-06-01
tags: [observability, performance, debugging]
---
```

### Graph Visualization

Graph visualization turns your note network into a visual map:

**Obsidian graph view features:**
- **Force-directed layout**: Nodes repel, edges attract
- **Filtering**: By tag, folder, search term
- **Grouping**: Color by tag, folder, or custom rules
- **Local graph**: Show connections around one note
- **Neighborhood**: Show N levels of connections

**Example visualization use cases:**
- Find orphan notes (nodes with no edges)
- Identify knowledge clusters
- Discover bridging concepts
- Spot densely connected areas (core knowledge)
- Track knowledge growth over time

### Markdown Knowledge Graphs

You can build a knowledge graph purely in Markdown by using structured metadata and links:

```markdown
# Distributed Systems Knowledge Graph

## Nodes (Concepts)
- [[CAP Theorem]] — type: theory
- [[Consistency Models]] — type: concept
- [[Raft Consensus]] — type: algorithm
- [[Apache Cassandra]] — type: technology
- [[Distributed Tracing]] — type: practice

## Edges (Relationships)
- [[CAP Theorem]] restrains [[Consistency Models]]
- [[Raft Consensus]] implements [[Consensus Algorithm]]
- [[Apache Cassandra]] is AP in [[CAP Theorem]]
- [[Distributed Tracing]] requires [[Instrumentation]]
```

### Obsidian Graph View

The Obsidian graph view is the most accessible knowledge graph tool for Markdown users.

**Graph view tips:**
1. Use local graph (Ctrl+Shift+G) for focused exploration
2. Color by tag for instant categorization
3. Use filters to remove noise (exclude daily notes)
4. Search to highlight specific nodes
5. Use "Open in new pane" to navigate from graph
6. Detect structural patterns (hub notes, isolated clusters)

### Roam Research Graphs

Roam Research popularized the block-level graph for knowledge management. While Roam uses its own format (not plain Markdown), its concepts influenced the entire PKM space:

- **Block references**: Reference any block anywhere
- **Block embedding**: Live embed blocks across pages
- **Attribute values**: Structured data on any block
- **Graph database**: Every piece of content is queryable

### Neo4j for Documentation Graphs

For enterprise-scale knowledge graphs, Neo4j (a graph database) can power documentation knowledge graphs:

```cypher
// Create nodes
CREATE (cap:Concept {name: 'CAP Theorem', year: 2000})
CREATE (dist:Concept {name: 'Distributed Systems'})
CREATE (doc:Document {title: 'Brewers CAP Keynote', type: 'paper'})

// Create relationships
CREATE (cap)-[:BELONGS_TO]->(dist)
CREATE (doc)-[:EXPLAINS]->(cap)
CREATE (cap)-[:RELATES_TO]->(cons:Concept {name: 'Consistency'})

// Query the graph
MATCH (c:Concept)-[:RELATES_TO]->(related)
WHERE c.name = 'CAP Theorem'
RETURN c, related
```

**Integration with Markdown:**
Export Markdown notes, extract entities, import to Neo4j, query and visualize.

### Building a Documentation Knowledge Graph

**Step 1: Define your schema**

```yaml
Node types:
- Concept (fundamental ideas)
- Technology (tools, frameworks)
- Document (articles, books, docs)
- Person (authors, experts)
- Project (internal initiatives)

Relationship types:
- relates_to (general connection)
- implements (technology implements concept)
- explains (document explains concept)
- authored_by (document by person)
- depends_on (concept depends on concept)
```

**Step 2: Extract entities from notes**

```python
# Pseudocode for entity extraction
for note in vault.get_all_notes():
    for link in note.extract_wiki_links():
        graph.add_edge(note.id, link.target, "links_to")
    for tag in note.extract_tags():
        graph.add_node(tag, type="tag")
        graph.add_edge(note.id, tag, "tagged_with")
```

**Step 3: Visualize the graph**

Use Obsidian graph view for a quick overview, or export to a tool like:
- **Gephi**: Desktop graph visualization
- **Neo4j Browser**: Web-based graph exploration
- **D3.js**: Custom web visualization
- **Graphviz**: Static graph rendering

**Step 4: Query the graph for insights**

```markdown
## Knowledge Graph Queries

### Most connected notes
```dataview
TABLE length(file.outlinks) + length(file.inlinks) AS connections
FROM ""
SORT connections DESC
LIMIT 10
```

### Orphan notes (no connections)
```dataview
TABLE file.path
FROM ""
WHERE length(file.outlinks) = 0 AND length(file.inlinks) = 0
```

### Topic clusters
```dataview
TABLE rows.file.link AS notes
FROM #distributed-systems
GROUP BY status
```
```

---

## 13.8 Documentation Strategy

### Aligning Docs with Business Goals

Documentation must serve business objectives:

| Business Goal | Documentation Strategy |
|---------------|----------------------|
| Faster onboarding | Getting started guides, tutorials |
| Reduced support tickets | Troubleshooting guides, FAQs |
| Compliance | Process documentation, audit trails |
| Developer productivity | API references, code examples |
| Knowledge retention | Knowledge base, runbooks |
| Product adoption | Feature documentation, use cases |

**Key performance questions:**
- Are we documenting the right things?
- Are our docs accessible when needed?
- Are docs being maintained?
- Are docs reducing questions/tickets?

### Documentation Maturity Model

#### Level 1: No Documentation

**Characteristics:**
- Knowledge only exists in people's heads
- No written processes
- Tribal knowledge culture
- High bus factor
- Every issue is a crisis

**Risks:**
- Key person dependencies
- Onboarding takes months
- Repeated mistakes
- No historical context

#### Level 2: Basic Documentation

**Characteristics:**
- Some docs exist but are scattered
- No standard format or location
- Docs are often outdated
- Written under pressure
- No ownership

**Risks:**
- Low trust in documentation
- People still ask instead of searching
- Documentation debt grows

#### Level 3: Organized Documentation

**Characteristics:**
- Centralized repository (wiki, knowledge base)
- Standard templates and formats
- Regular review cycle
- Assigned owners
- Searchable

**Practices:**
- Consistent Markdown formatting
- Folder/documentation structure
- Basic linking between docs
- Version controlled (Git)

#### Level 4: Managed Documentation

**Characteristics:**
- Documentation KPIs tracked
- Feedback loop from users
- Automated quality checks
- Documentation part of definition of done
- Dedicated documentation role

**Practices:**
- Documentation review in code reviews
- Automated diagram generation
- Broken link checking
- Usage analytics

#### Level 5: Continuous Documentation

**Characteristics:**
- Documentation is treated as a product
- Automated from code/configuration
- Continuous updates via CI/CD
- Personalized documentation
- AI-assisted documentation
- Embedded in workflows

**Practices:**
- Documentation-as-code
- API docs generated from OpenAPI
- Architecture docs from infrastructure
- Automated deployment documentation
- User feedback integrated

### Documentation KPIs

| KPI | Measurement | Target |
|-----|-------------|--------|
| **Coverage** | % of features with docs | >90% |
| **Freshness** | % of docs updated in last 90 days | >80% |
| **Findability** | % of searches returning useful results | >85% |
| **Completion** | % of users completing tutorials | >70% |
| **Support deflection** | Support tickets avoided by docs | >20% |
| **Time-to-value** | Time to first successful API call | <5 minutes |
| **Net Promoter Score** | "Would you recommend our docs?" | >30 |
| **Broken links** | Count of broken links | 0 |
| **Update frequency** | Days since last update | <30 days |

### Stakeholder Alignment

Documentation has multiple stakeholders with different needs:

| Stakeholder | Needs | Success Metric |
|-------------|-------|----------------|
| **End users** | Find answers quickly | Time-to-resolution |
| **Developers** | Accurate, up-to-date references | Fewer questions, faster integration |
| **Support team** | Troubleshooting guides | Ticket deflection |
| **Product managers** | Feature adoption docs | Usage metrics |
| **Compliance** | Process documentation | Audit pass rate |
| **Leadership** | ROI of documentation | Cost savings, reduced risk |
| **Documentation team** | Clear requirements, feedback | Documentation quality |

**Alignment strategies:**
1. Regular stakeholder meetings
2. Documentation roadmap reviews
3. Usage analytics shared with stakeholders
4. Feedback surveys
5. Documentation demos

---

## 13.9 Information Architecture

Information Architecture (IA) is the practice of organizing, structuring, and labeling content to support usability and findability.

### Organization Systems

Four main organization systems:

| System | Structure | Use Case |
|--------|-----------|----------|
| **Hierarchical** | Tree structure, parent-child | Most websites, file systems |
| **Sequential** | Step-by-step, linear | Tutorials, workflows |
| **Matrix** | Multiple categories, cross-links | Knowledge bases, wikis |
| **Network** | Web of connections, no hierarchy | Digital gardens, Zettelkasten |

#### Hierarchical

```
/Products
  /Software
    /API
    /CLI
    /SDK
  /Hardware
    /Sensors
    /Gateways
/Services
  /Consulting
  /Support
  /Training
/Company
  /About
  /Careers
  /Blog
```

Best for: Content with clear categories.

#### Sequential

```
1. Introduction
2. Prerequisites
3. Installation
4. Configuration
5. First Use
6. Advanced Features
7. Troubleshooting
8. Reference
```

Best for: Tutorials and guides.

#### Matrix

```
Categories x Tags x Formats:
- Category: API / CLI / SDK
- Tag: beginner / intermediate / advanced
- Format: tutorial / reference / guide

Content appears in multiple categories simultaneously.
```

Best for: Large content collections with multiple dimensions.

#### Network

```
Distributed Systems
    CAP Theorem -- Consistency Models
    Raft Consensus -- Leader Election
    Event Sourcing -- CQRS

Each note links to others without hierarchy.
```

Best for: Complex, interconnected knowledge.

### Labeling Systems

Labels are the terms users see and search for:

**Label types:**
- **Navigation labels**: Section titles, menu items
- **Link labels**: Descriptive text for hyperlinks
- **Heading labels**: Content headings
- **Tag labels**: Categorization metadata
- **Metadata labels**: Data fields

**Label best practices:**
- Use your users' vocabulary, not internal jargon
- Be consistent across the entire system
- Be concise but descriptive
- Avoid ambiguous terms
- Test labels with users
- Update labels as vocabulary evolves

### Navigation Systems

**Global navigation:**
- Main sections
- Always accessible
- Consistent across the site

**Local navigation:**
- Within a section
- Table of contents
- Related links

**Contextual navigation:**
- Embedded links in content
- See also sections
- Related articles

**Supplemental navigation:**
- Search
- Site map
- Index
- Recent changes

### Search Systems

Search is often the primary way users find documentation:

**Search optimization:**
1. Full-text indexing of all content
2. Metadata indexing (tags, titles, descriptions)
3. Synonym expansion ("car" matches "automobile")
4. Fuzzy matching for typos
5. Scoring by relevance (titles > headings > body)
6. Faceted search (filter by type, date, tag)
7. Search analytics and improvement

**Markdown search tools:**
- Lunr.js (client-side search)
- Fuse.js (fuzzy search)
- Algolia (hosted search)
- Obsidian built-in search
- Omnisearch plugin
- Dataview queries

### Content Modeling

Content modeling defines the structure of content types:

```yaml
Content Type: Tutorial
Properties:
  - title: string (required)
  - description: string (required, max 160 chars)
  - difficulty: enum [beginner, intermediate, advanced]
  - topics: array of Topic
  - prerequisites: array of Tutorial
  - estimated_time: duration
  - steps: array of Step
  - updated: date

Content Type: API Reference
Properties:
  - endpoint: string (required)
  - method: enum [GET, POST, PUT, DELETE, PATCH]
  - description: string
  - parameters: array of Parameter
  - request_body: Body
  - responses: array of Response
  - examples: array of Example
```

In Markdown frontmatter:

```markdown
---
title: Getting Started with the API
description: Learn how to make your first API call
difficulty: beginner
topics: [authentication, endpoints, rate-limiting]
prerequisites: [account-setup]
estimated_time: 15 min
---
```

### Metadata Strategies

Metadata makes content findable and manageable:

**Standard metadata fields:**
```yaml
---
title: The title of the document
description: Brief summary for search results
author: Document creator
created: 2026-06-03
updated: 2026-06-03
version: 1.2.0
status: draft | review | published | archived
tags: [tag1, tag2, tag3]
category: Category Name
audience: developers | admins | end-users
difficulty: beginner | intermediate | advanced
---
```

**Custom metadata (domain-specific):**
```yaml
---
# For API documentation
endpoint: /api/v2/users
method: POST
rate_limit: 100/hour

# For project documentation
sprint: 12
epic: user-authentication
owner: @alice

# For compliance documentation
regulation: SOC2
control: CC6.1
review_date: 2026-09-01
---
```

### Taxonomies vs Folksonomies

| Aspect | Taxonomy | Folksonomy |
|--------|----------|------------|
| Definition | Controlled, hierarchical classification | User-generated, flat tagging |
| Creation | Top-down by experts | Bottom-up by users |
| Consistency | High | Low |
| Flexibility | Low | High |
| Scalability | Requires maintenance | Scales naturally |
| Findability | Precise results | Serendipitous discovery |
| Example | Dewey Decimal System | Twitter hashtags |
| Best for | Large, formal content collections | Community-driven content |
| Markdown tool | Controlled tag list | Free-form tags |

---

## 13.10 Long-term Knowledge Management

### Knowledge Decay and Freshness

Knowledge has a shelf life. Information decays when:

- Technologies change
- Processes evolve
- Personnel leave
- Products are deprecated
- Regulations are updated

**Knowledge decay rates by type:**

| Knowledge Type | Typical Decay Rate | Example |
|----------------|-------------------|---------|
| Technical specs | 6-12 months | API endpoints, configuration |
| Process docs | 12-24 months | Workflows, checklists |
| Conceptual docs | 24-60 months | Architecture decisions, patterns |
| Reference docs | 6-18 months | Version-specific documentation |
| Historical docs | 5+ years | Post-mortems, retrospectives |

### Documentation Maintenance Schedules

**Continuous (every day):**
- Update docs with code changes
- Fix broken links
- Answer questions with doc updates

**Weekly:**
- Review new docs for accuracy
- Update status indicators
- Process documentation feedback

**Monthly:**
- Freshness audit (flag docs older than threshold)
- Update outdated content
- Archive obsolete docs
- Review analytics

**Quarterly:**
- Full content audit
- Review information architecture
- Update templates and standards
- Stakeholder review

**Annually:**
- Full documentation overhaul
- Archive and purge
- Maturity assessment
- Documentation roadmap

### Archive Strategies

**When to archive:**
- Technology is deprecated
- Process no longer applies
- Product version is end-of-life
- Content is superseded

**How to archive in Markdown:**

```markdown
---
title: Legacy API v1 Migration Guide
status: archived
archived_date: 2026-06-03
superseded_by: api-v2-migration-guide.md
reason: API v1 deprecated as of June 2026
---

# Legacy API v1 Migration Guide (Archived)

This guide is for historical reference only.
Please refer to the [[API v2 Migration Guide]] for current instructions.
```

**Archive folder structure:**

```
docs/
├── modules/
│   ├── 01-getting-started.md
│   └── ...
└── archive/
    ├── 2026/
    │   ├── legacy-api-v1.md
    │   └── old-onboarding.md
    └── 2025/
        ├── discontinued-product.md
        └── old-process.md
```

### Knowledge Transfer

**When knowledge transfer is critical:**
- Employee departure
- Role changes
- Team restructuring
- Project handoffs

**Knowledge transfer documentation:**

```markdown
# Knowledge Transfer: Alice Chen (Senior Backend Engineer)

## Current Responsibilities
- API Gateway maintenance and development
- Database performance optimization
- CI/CD pipeline management

## Key Knowledge Areas
1. **API Gateway** — Configuration at /infra/gateway/
2. **Database Tuning** — Runbooks in /runbooks/database/
3. **CI/CD** — Pipeline definitions in /infra/cicd/

## Contacts
- Secondary: Bob Smith (bob@example.com)
- Escalation: Carol Davis (carol@example.com)

## Transition Plan
- Week 1: Shadowing and documentation review
- Week 2: Paired work on active projects
- Week 3: Reverse shadowing (new person leads)
- Week 4: Independent work with review
```

### Documentation Debt

Like technical debt, documentation debt accumulates when documentation is deferred, skipped, or neglected.

**Types of documentation debt:**
1. **Missing documentation**: Features without docs
2. **Outdated documentation**: Docs that no longer match reality
3. **Inconsistent documentation**: Different formats, styles, or locations
4. **Duplicated documentation**: Multiple sources for the same information
5. **Inaccessible documentation**: Hard to find or search
6. **Untested documentation**: Code examples that don't work

**Managing documentation debt:**

```markdown
# Documentation Debt Register

| ID | Issue | Impact | Effort | Status |
|----|-------|--------|--------|--------|
| DD-001 | API v2 migration guide missing | High | 2 days | In progress |
| DD-002 | Deployment runbook outdated | High | 4 hours | Todo |
| DD-003 | README duplicated in 3 locations | Low | 1 hour | Backlog |
| DD-004 | Code examples use deprecated SDK | Medium | 1 day | Todo |

## Resolution Strategy
- High impact: Resolve within current sprint
- Medium impact: Resolve within quarter
- Low impact: Resolve opportunistically
```

### AI in Knowledge Management

AI is transforming knowledge management in several ways:

**AI-powered summarization:**
```markdown
## AI Summary
<!-- Summarized by AI on 2026-06-03 -->
This document describes the distributed tracing implementation using OpenTelemetry.
Key points: (1) Traces are sampled at 10% rate, (2) Jaeger is used for visualization,
(3) Custom spans are added for database queries. Review quarterly for accuracy.
```

**AI-powered Q&A:**
- Users ask natural language questions
- AI retrieves relevant documentation context
- Answers with citations to source documents

**AI-powered discovery:**
- Automatic topic clustering
- Content gap identification
- Related content recommendations
- Cross-reference suggestions

**AI-powered maintenance:**
- Detect outdated content
- Suggest updates based on code changes
- Auto-generate documentation stubs
- Check for broken links

**Implementation considerations:**
- AI accuracy must be validated
- Human review of AI-generated content is essential
- Privacy concerns when using cloud AI services
- Cost considerations for large documentation sets
- Balance automation with human judgment

---

## 13.11 Organizational Knowledge Management

### Internal Wikis

Internal wikis are the most common form of organizational KM.

**Wiki platforms for Markdown:**

| Platform | Type | Strengths |
|----------|------|-----------|
| GitHub Wiki | Git-based | Integrated with code, version control |
| GitLab Wiki | Git-based | Integrated with GitLab |
| Confluence | Commercial | Rich features, templates |
| Notion | Commercial | Modern, flexible |
| BookStack | Open source | Simple, focused |
| Wiki.js | Open source | Modern, extensible |
| DokuWiki | Open source | Mature, no database needed |
| Outline | Open source | Markdown-native, fast |

**Wiki structure best practices:**

```
wiki/
├── Home.md (main landing page)
├── Getting-Started.md
├── Guides/
│   ├── Development-Setup.md
│   ├── Deployment.md
│   └── Testing.md
├── Reference/
│   ├── API-Reference.md
│   ├── Configuration.md
│   └── Database-Schema.md
├── Processes/
│   ├── Code-Review.md
│   ├── Release-Process.md
│   └── Incident-Response.md
├── Team/
│   ├── Team-Structure.md
│   ├── Onboarding.md
│   └── Policies.md
└── Archive/
    └── Legacy-Systems.md
```

### Onboarding Documentation

Onboarding documentation is often the first interaction new team members have with your knowledge base.

**Onboarding document structure:**

```markdown
# Developer Onboarding Guide

## Week 1: Foundation
- [ ] Set up development environment (see [[Development Setup]])
- [ ] Make your first commit
- [ ] Read [[Architecture Overview]]
- [ ] Complete [[Security Training]]

## Week 2: First Feature
- [ ] Pick a small starter task
- [ ] Pair with a team member
- [ ] Submit your first PR
- [ ] Deploy to staging

## Week 3: Integration
- [ ] Take ownership of a component
- [ ] Learn the testing process
- [ ] Shadow on-call rotation

## Month 1: Independence
- [ ] Complete a full feature cycle
- [ ] Write a post-mortem
- [ ] Contribute to documentation
- [ ] Give a team presentation

## Key Contacts
- Manager: @manager
- Mentor: @mentor
- Team lead: @lead
- HR: @hr

## Essential Resources
- [[Architecture Overview]]
- [[Development Environment Setup]]
- [[Code Review Guidelines]]
- [[Deployment Runbook]]
- [[Incident Response Playbook]]
```

### SOP Documentation

Standard Operating Procedures (SOPs) ensure consistency and quality:

**SOP template:**

```markdown
# SOP: Database Backup and Recovery

## Purpose
Ensure all production databases are backed up and recoverable.

## Scope
All production PostgreSQL databases.

## Responsible
Database Administration Team

## Frequency
- Full backup: Daily at 02:00 UTC
- Incremental: Every 6 hours
- Recovery test: Monthly

## Prerequisites
1. Access to backup server
2. Database admin credentials
3. Backup monitoring access

## Procedure: Daily Backup
1. SSH into backup server: `ssh backup@backup-server`
2. Run backup script: `./scripts/backup.sh --full`
3. Verify backup file: `ls -la /backups/latest/`
4. Check log: `tail -100 /var/log/backup.log`
5. Verify checksum: `sha256sum /backups/latest/*.dump`

## Procedure: Recovery
1. Stop application: `systemctl stop app`
2. Restore database: `./scripts/restore.sh --latest`
3. Verify data: `psql -c "SELECT count(*) FROM users;"`
4. Start application: `systemctl start app`
5. Verify application health: `curl http://localhost:8080/health`

## Troubleshooting
- Backup fails: Check disk space with `df -h`
- Recovery fails: Check PostgreSQL logs: `journalctl -u postgresql`
- For emergencies, contact DBA on-call: @dba-oncall

## Related Documents
- [[Disaster Recovery Plan]]
- [[Database Architecture]]
- [[Backup Monitoring Runbook]]
```

### Post-Mortem Documentation

Post-mortems capture lessons from incidents and outages:

```markdown
# Post-Mortem: API Outage June 3, 2026

## Incident Summary
- **Date**: 2026-06-03
- **Duration**: 47 minutes (14:23 - 15:10 UTC)
- **Impact**: All API requests returned 502 errors
- **Users affected**: ~50,000
- **Severity**: SEV-1

## Timeline
| Time (UTC) | Event |
|------------|-------|
| 14:23 | PagerDuty alert: API health check failure |
| 14:25 | Engineer acknowledges alert |
| 14:28 | Identified: Database connection pool exhaustion |
| 14:32 | Deployed emergency connection pool increase |
| 14:45 | Full recovery confirmed |
| 15:10 | Monitoring stable, incident closed |

## Root Cause
A deployment at 14:00 increased the worker pool from 20 to 100 without
increasing the database connection pool, which was still configured for 20
concurrent connections. When traffic spiked, the 20 database connections
were saturated, and all workers were blocked waiting for connections.

## Action Items
- [ ] Add connection pool monitoring alert (P2)
- [ ] Update deployment checklist to include connection pool review (P1)
- [ ] Implement connection pool autoscaling (P2)
- [ ] Add database connection pool test to staging (P1)

## Lessons Learned
1. Deployment checklist needs to include dependency review
2. Connection pool limits should scale with worker count
3. Need better monitoring for connection pool utilization

## Related Documents
- [[Deployment Checklist]]
- [[Database Configuration Guide]]
- [[Monitoring Runbook]]
```

### Decision Records (ADRs)

Architecture Decision Records (ADRs) capture important decisions and their rationale:

```markdown
# ADR-0012: Adopt OpenTelemetry for Distributed Tracing

## Status
Accepted

## Context
We need distributed tracing across our microservices. Currently, each service
uses a different tracing library, making end-to-end tracing impossible.

## Decision
Adopt OpenTelemetry as the standard tracing framework across all services.

## Rationale
- Industry standard with broad community support
- Vendor-neutral (can export to Jaeger, Zipkin, Datadog, etc.)
- Supports all our languages (Go, TypeScript, Python)
- Provides both tracing and metrics
- Automatic instrumentation for common libraries
- Strong backing from CNCF

## Consequences
- All services need OpenTelemetry SDK integration
- Existing custom tracing must be migrated
- Team training required on OpenTelemetry concepts
- Jaeger infrastructure for trace storage and visualization

## Related ADRs
- ADR-0005: Microservices Architecture Decision
- ADR-0008: Observability Strategy

## Reviewed
2026-06-03 — reviewed and still valid
```

### Runbooks

Runbooks provide step-by-step procedures for common operational tasks:

```markdown
# Runbook: High CPU on Application Server

## Alert Condition
CPU usage > 90% for 5+ minutes on any application server.

## Severity
P2 (or P1 if affecting customer experience)

## Steps

### 1. Identify the affected server
```bash
kubectl get pods -n production -o wide | grep app
kubectl top pods -n production | sort -k2 -n
```

### 2. Check what is consuming CPU
```bash
kubectl exec -it pod-name -- top -bn1
kubectl exec -it pod-name -- ps aux --sort=-%cpu | head -10
```

### 3. Check application logs
```bash
kubectl logs pod-name --tail=100 --since=10m
kubectl logs pod-name --previous | tail -50
```

### 4. Common causes and resolutions

| Cause | Indicator | Resolution |
|-------|-----------|------------|
| Traffic spike | Elevated request count | Scale up replicas |
| Memory leak | Increasing memory usage | Restart pod, file bug |
| Slow query | Database slow logs | Optimize query, add index |
| Background job | Specific worker process | Throttle or defer job |

### 5. Escalation
If unresolved after 15 minutes, escalate to:
- Primary on-call: @oncall
- Engineering manager: @eng-manager

## Verification
```bash
kubectl top pods -n production | grep app
# CPU should be below 80%
```

## Related
- [[Database Performance Runbook]]
- [[Auto-scaling Configuration]]
- [[Capacity Planning Guide]]
```

### Playbooks

Playbooks are strategic guides for handling complex scenarios:

```markdown
# Playbook: Major Version Migration

## Overview
This playbook covers migrating a service from version N to N+1 with zero downtime.

## Pre-Migration (2 weeks before)
- [ ] Audit all API consumers
- [ ] Create migration guide for consumers
- [ ] Set up versioned endpoints (/v1/, /v2/)
- [ ] Implement dual-write for data changes
- [ ] Run shadow traffic with v2
- [ ] Performance benchmark v2 vs v1
- [ ] Create rollback plan

## Migration Day
### Phase 1: Canary (10% traffic)
1. Route 10% of traffic to v2
2. Monitor error rates, latency, and throughput
3. Compare metrics with v1 baseline
4. Duration: 24 hours minimum

### Phase 2: Gradual Ramp (50% traffic)
1. Increase to 50% traffic
2. Monitor for regression
3. Collect feedback from early consumers
4. Duration: 48 hours

### Phase 3: Full Migration (100% traffic)
1. Route all traffic to v2
2. Keep v1 running in read-only mode
3. Monitor for issues
4. Duration: 1 week

## Post-Migration
- [ ] Deprecate v1 endpoints (add deprecation headers)
- [ ] Update documentation to v2 defaults
- [ ] Archive v1 code
- [ ] Notify all consumers of v1 deprecation timeline
- [ ] Remove v1 infrastructure after deprecation period

## Rollback Triggers
- Error rate increase > 1%
- Latency p99 increase > 20%
- Any data integrity issues
- Customer complaints > threshold

## Rollback Procedure
1. Revert DNS/routing to v1
2. Run data reconciliation
3. Notify stakeholders
4. File incident report

## Success Criteria
- Zero downtime during migration
- No data loss
- Latency within 10% of v1
- Error rate <= v1 baseline
- All consumers migrated within 30 days
```

---

## 13.12 Exercises

### Exercise 1: Build a Zettelkasten

Create a Zettelkasten system with 10 atomic notes on a topic of your choice (e.g., distributed systems, programming languages, project management). Include:
- 8 atomic notes with unique IDs
- 1 connection note linking at least 3 atomic notes
- 1 structure note organizing the topic
- Apply progressive summarization to one note

### Exercise 2: Implement PARA in Markdown

Create a folder structure implementing the PARA method for your personal or professional life. Write at least 3 notes in each category (Projects, Areas, Resources, Archives). Add a README.md explaining your system.

### Exercise 3: Create a Digital Garden

Set up a Quartz-based digital garden with:
- Index/landing page
- 5 interconnected notes at different growth stages (seedling, growing, evergreen)
- Bi-directional links between all notes
- Deploy to GitHub Pages

### Exercise 4: Set Up an Obsidian Vault

Create an Obsidian vault structure with:
- Template for permanent notes, daily notes, and projects
- 5 complete permanent notes with frontmatter
- Dataview queries to list notes by status and tag
- A graph-view-ready set of interconnected notes

### Exercise 5: Create a Knowledge Graph

Build a knowledge graph from 20 Markdown notes on a complex topic. Map the connections and:
- Identify the 3 most connected notes (hub nodes)
- Find any orphan notes
- Create a structure note that organizes the graph
- Export and visualize using Graphviz

### Exercise 6: Design a Documentation Maturity Assessment

Create a Markdown-based assessment tool to evaluate your team's documentation maturity. Include:
- Questions for each maturity level (1-5)
- Scoring rubric
- Action plan template for improvement
- Example completed assessment

### Exercise 7: Write ADRs

Write 3 Architecture Decision Records for a real or fictional system:
- ADR 1: Technology choice (e.g., database, framework)
- ADR 2: Architecture pattern (e.g., microservices, event-driven)
- ADR 3: Tooling decision (e.g., monitoring, CI/CD)
Include context, decision, rationale, and consequences for each.

### Exercise 8: Create an Onboarding Guide

Write a complete onboarding guide for a new developer joining your team. Include:
- Week-by-week plan for the first month
- Essential resources and links
- Key contacts
- First task instructions
- Success criteria for each week

### Exercise 9: Runbook and Playbook

Create one runbook and one playbook for your domain:
- **Runbook**: A step-by-step procedure for a common operational task (e.g., restarting a service, database recovery)
- **Playbook**: A strategic guide for a complex scenario (e.g., major version migration, incident response)
Both must include troubleshooting steps and escalation paths.

### Exercise 10: Full Knowledge Management System

Design and implement a complete personal knowledge management system:
- Choose your methodology (Zettelkasten, PARA, or hybrid)
- Create the folder structure
- Write templates for all note types
- Create 15+ interconnected notes
- Implement a review schedule
- Set up Git-based version control
- Document your system in a README.md

---

## 13.13 Quiz

### Question 1
What does the acronym CODE stand for in Tiago Forte's Second Brain methodology?

A) Create, Observe, Decide, Execute
B) Capture, Organize, Distill, Express
C) Collect, Order, Develop, Explain
D) Connect, Outline, Draft, Edit

**Answer**: B) Capture, Organize, Distill, Express

### Question 2
Which organizational method does the Second Brain use?

A) GTD (Getting Things Done)
B) PARA (Projects, Areas, Resources, Archives)
C) KANBAN
D) SCRUM

**Answer**: B) PARA (Projects, Areas, Resources, Archives)

### Question 3
What is the core principle of a Zettelkasten note?

A) Each note should be as long as possible
B) Each note contains exactly one idea (atomicity)
C) Notes should only link to the index
D) Notes must be written in German

**Answer**: B) Each note contains exactly one idea (atomicity)

### Question 4
Which of the following is NOT a note type in the Zettelkasten method?

A) Atomic notes
B) Connection notes
C) Sequence notes
D) Daily notes

**Answer**: D) Daily notes

### Question 5
What is a digital garden?

A) A blog with chronological posts
B) A collection of publicly available notes that grow over time
C) A wiki with strictly structured articles
D) A social media platform

**Answer**: B) A collection of publicly available notes that grow over time

### Question 6
Which static site generator is specifically designed for digital gardens?

A) Jekyll
B) Hugo
C) Quartz
D) Next.js

**Answer**: C) Quartz

### Question 7
What is the primary file format used by Obsidian?

A) Rich Text Format (RTF)
B) Plain Markdown (.md)
C) Obsidian's proprietary format
D) JSON

**Answer**: B) Plain Markdown (.md)

### Question 8
Which Obsidian plugin allows querying notes like a database?

A) Excalidraw
B) Kanban
C) Dataview
D) Calendar

**Answer**: C) Dataview

### Question 9
What is progressive summarization?

A) Automatically summarizing notes with AI
B) Distilling captured information into increasingly valuable layers
C) Deleting old notes to reduce clutter
D) Converting notes to different formats

**Answer**: B) Distilling captured information into increasingly valuable layers

### Question 10
What is the purpose of a Map of Content (MOC)?

A) To show a geographical map of note locations
B) To provide an overview and navigation of a topic area
C) To visualize the folder structure
D) To track document metadata

**Answer**: B) To provide an overview and navigation of a topic area

### Question 11
Which maturity level describes an organization where documentation is treated as a product and automated via CI/CD?

A) Level 2: Basic Documentation
B) Level 3: Organized Documentation
C) Level 4: Managed Documentation
D) Level 5: Continuous Documentation

**Answer**: D) Level 5: Continuous Documentation

### Question 12
What does ADR stand for in organizational knowledge management?

A) Automated Document Review
B) Architecture Decision Record
C) Application Development Roadmap
D) Advanced Documentation Request

**Answer**: B) Architecture Decision Record

### Question 13
What is the difference between a taxonomy and a folksonomy?

A) Taxonomies are hierarchical and controlled; folksonomies are user-generated and flat
B) Taxonomies are for images; folksonomies are for text
C) There is no difference
D) Taxonomies are generated by AI; folksonomies are generated manually

**Answer**: A) Taxonomies are hierarchical and controlled; folksonomies are user-generated and flat

### Question 14
What is documentation debt?

A) The cost of hosting documentation
B) The accumulation of missing, outdated, or inconsistent documentation
C) The time spent writing documentation
D) The number of documents in a knowledge base

**Answer**: B) The accumulation of missing, outdated, or inconsistent documentation

### Question 15
Which knowledge management tool uses block-level references and a graph database approach?

A) Obsidian
B) Notion
C) Roam Research
D) Evernote

**Answer**: C) Roam Research
