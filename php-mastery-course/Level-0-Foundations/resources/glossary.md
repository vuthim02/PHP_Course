# Level 0 — Glossary of Foundational Terms

## Hardware

| Term | Definition |
|------|------------|
| **CPU** | Central Processing Unit — executes instructions; contains cores, cache |
| **RAM** | Random Access Memory — volatile, fast storage for active programs |
| **SSD / HDD** | Solid-State Drive / Hard Disk Drive — persistent storage |
| **Cache (L1/L2/L3)** | Small, ultra-fast memory layers between CPU and RAM |
| **Clock Speed** | GHz rating — how many cycles per second the CPU executes |
| **Instruction Set** | The primitive operations a CPU understands (x86, ARM) |

## Operating System

| Term | Definition |
|------|------------|
| **Kernel** | Core of the OS — manages hardware, processes, memory |
| **Process** | A running program — has its own memory space |
| **Thread** | A lightweight sub-process sharing memory with other threads |
| **Syscall** | Request from user-space to kernel (open file, send network) |
| **File Descriptor** | A handle used to reference open files, sockets, pipes |
| **Context Switch** | Switching CPU from one process to another — expensive |
| **Virtual Memory** | Each process sees its own address space; OS maps to physical RAM |

## Networking

| Term | Definition |
|------|------------|
| **TCP/IP** | The protocol stack of the internet |
| **DNS** | Domain Name System — translates `example.com` to IP |
| **HTTP** | HyperText Transfer Protocol — request/response protocol for the web |
| **TLS** | Transport Layer Security — encrypts HTTP into HTTPS |
| **IP Address** | Numeric identifier for a device on a network |
| **Port** | Logical endpoint (80 = HTTP, 443 = HTTPS, 22 = SSH) |
| **Latency** | Time for a packet to travel from source to destination |
| **Bandwidth** | Amount of data that can be transferred per second |

## Web

| Term | Definition |
|------|------------|
| **URL / URI** | Uniform Resource Locator / Identifier — an address on the web |
| **HTTP Method** | GET (read), POST (create), PUT (replace), PATCH (update), DELETE |
| **Status Code** | 3-digit response code: 2xx success, 3xx redirect, 4xx client error, 5xx server error |
| **Cookie** | Small piece of data stored by the browser, sent with every request |
| **Session** | Server-side state associated with a user, keyed by a cookie |
| **Cache** | Storing responses to avoid re-fetching |
| **CDN** | Content Delivery Network — geographically distributed cache |

## Databases

| Term | Definition |
|------|------------|
| **DBMS** | Database Management System — software that manages databases |
| **SQL** | Structured Query Language — language for relational databases |
| **Table** | A collection of rows (records) and columns (fields) |
| **Index** | Data structure that speeds up lookups (like a book index) |
| **Transaction** | Group of operations that succeed or fail atomically |
| **ACID** | Atomicity, Consistency, Isolation, Durability — guarantees of transactions |
| **Normalization** | Organizing tables to reduce redundancy |

## Version Control

| Term | Definition |
|------|------------|
| **Repository** | A directory tracked by Git — contains all history |
| **Commit** | A snapshot of changes at a point in time |
| **Branch** | A parallel line of development |
| **Merge** | Combining changes from different branches |
| **Remote** | A Git repository hosted elsewhere (GitHub, GitLab) |
| **Pull Request** | A request to merge changes from one branch into another |

## Development

| Term | Definition |
|------|------------|
| **IDE** | Integrated Development Environment — code editor with debugging, refactoring |
| **CLI** | Command-Line Interface — interacting with software via terminal |
| **Package Manager** | Tool that installs libraries and their dependencies (npm, Composer, pip) |
| **Environment** | The context where software runs (local, staging, production) |
| **Environment Variable** | OS-level configuration passed to applications (`DATABASE_URL`, `APP_ENV`) |
| **Debugging** | Finding and fixing bugs using tools like breakpoints, logs, dumps |
