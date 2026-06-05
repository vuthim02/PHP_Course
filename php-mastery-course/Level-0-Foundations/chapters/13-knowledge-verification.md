# Chapter 13: Knowledge Verification

## Complete Assessment Suite for Level 0

This chapter contains quizzes, coding challenges, real-world problems, and a capstone project to verify your understanding of everything covered in Level 0.

---

## 13.1 Quiz Questions (20)

### Beginner Level (Questions 1-10)

1. **What is a computer's CPU responsible for?**
   - a) Storing files permanently
   - b) Executing instructions
   - c) Displaying graphics
   - d) Connecting to the internet

2. **Which memory type is volatile (loses data when power is off)?**
   - a) SSD
   - b) HDD
   - c) RAM
   - d) ROM

3. **What does DNS stand for?**
   - a) Digital Network System
   - b) Domain Name System
   - c) Data Network Service
   - d) Dynamic Name Server

4. **Which HTTP method is used to retrieve data?**
   - a) POST
   - b) PUT
   - c) GET
   - d) DELETE

5. **What port does HTTPS use by default?**
   - a) 80
   - b) 443
   - c) 8080
   - d) 3306

6. **Which of the following is a private IP address?**
   - a) 8.8.8.8
   - b) 192.168.1.1
   - c) 142.250.190.78
   - d) 1.1.1.1

7. **What does TCP stand for?**
   - a) Transmission Control Protocol
   - b) Transfer Communication Protocol
   - c) Terminal Connection Protocol
   - d) Transport Control Process

8. **Which component of a computer stores data permanently?**
   - a) CPU
   - b) RAM
   - c) SSD
   - d) GPU

9. **What is an operating system?**
   - a) A program that manages computer hardware and software
   - b) A web browser
   - c) A database management system
   - d) A programming language

10. **Which HTTP status code means "Not Found"?**
    - a) 200
    - b) 301
    - c) 404
    - d) 500

### Intermediate Level (Questions 11-15)

11. **What is a context switch in an operating system?**
    - a) Switching between keyboard layouts
    - b) Saving and loading process states when switching between processes
    - c) Changing the desktop wallpaper
    - d) Switching between user accounts

12. **What is the purpose of the PHP-FPM process manager?**
    - a) To compile PHP code
    - b) To manage PHP worker processes for handling web requests
    - c) To format PHP code
    - d) To install PHP extensions

13. **What does ACID stand for in database transactions?**
    - a) Atomicity, Consistency, Isolation, Durability
    - b) Automatic, Consistent, Integrated, Durable
    - c) Atomic, Complete, Isolated, Direct
    - d) Application, Connection, Integration, Data

14. **What is a B-tree index used for?**
    - a) Formatting SQL queries
    - b) Speeding up data retrieval in databases
    - c) Creating database backups
    - d) Encrypting database data

15. **What is the MVC pattern?**
    - a) Model-View-Controller
    - b) Main-View-Component
    - c) Module-Validation-Configuration
    - d) Memory-Variable-Cache

### Advanced Level (Questions 16-20)

16. **What is the difference between a process and a thread?**
    - a) Processes share memory, threads don't
    - b) Processes have isolated memory, threads share memory within a process
    - c) There is no difference
    - d) Threads are faster than processes in all cases

17. **How does virtual memory work?**
    - a) It creates fake memory that doesn't exist
    - b) It maps virtual addresses to physical RAM using page tables
    - c) It stores all data on the hard drive
    - d) It duplicates RAM for backup

18. **What is the N+1 query problem?**
    - a) Running N+1 queries instead of optimizing with JOINs
    - b) Using too many database connections
    - c) Querying tables with N indices
    - d) Loading N+1 pages of results

19. **What is OPcache and how does it improve PHP performance?**
    - a) It caches database queries
    - b) It stores compiled PHP opcodes in shared memory
    - c) It compresses PHP output
    - d) It optimizes network requests

20. **What is the difference between HTTP/1.1 and HTTP/2?**
    - a) HTTP/2 is faster and supports multiplexing
    - b) HTTP/2 doesn't use TCP
    - c) HTTP/2 only works with HTTPS
    - d) HTTP/2 doesn't support headers

---

## 13.2 Coding Challenges (10)

### Challenge 1: System Information Script
Write a PHP CLI script that displays:
- Current PHP version
- Memory limit
- Available disk space
- Current time in UTC

### Challenge 2: HTTP Request Analyzer
Create a PHP script that captures and displays all incoming HTTP request information (method, URI, headers, body).

### Challenge 3: Simple Router
Implement a basic PHP router that can match `GET /users`, `GET /users/{id}`, and `POST /users` with callable handlers.

### Challenge 4: PDO Database Wrapper
Create a database class using PDO with methods for:
- `findById($table, $id)`
- `findAll($table, $page, $perPage)`
- `create($table, $data)`
- `update($table, $id, $data)`
- `delete($table, $id)`

### Challenge 5: File Upload Handler
Write a secure file upload handler that:
- Validates file type (images only)
- Validates file size (max 5MB)
- Renames files to prevent conflicts
- Stores files outside web root

### Challenge 6: Session-based Authentication
Build a login/logout system using PHP sessions with:
- Password hashing
- Session regeneration on login
- CSRF token protection

### Challenge 7: API Response Formatter
Create a JSON response helper with:
- Proper HTTP status codes
- CORS headers
- Security headers
- Error formatting

### Challenge 8: Database Seeder
Write a script that:
- Connects to a database
- Creates a `users` table if not exists
- Inserts 1000 fake users using Faker

### Challenge 9: Performance Benchmark
Create a script that benchmarks:
- Array vs object property access
- Single vs prepared statement queries
- File read vs direct database read

### Challenge 10: CLI Portfolio Project Generator
Build a CLI tool that scaffolds a PHP project with:
- Directory structure
- composer.json
- .env.example
- Basic routing
- PDO connection

---

## 13.3 Real-World Problems (5)

### Problem 1: Memory Exhaustion
**Scenario:** Your PHP application crashes with "Allowed memory size exhausted" when processing a CSV import file with 500,000 rows.

**Tasks:**
1. Diagnose why this happens
2. Write a solution using streaming
3. Calculate the minimum memory needed for your solution
4. Add progress tracking

### Problem 2: Slow Database Query
**Scenario:** A page that shows user orders takes 30 seconds to load. The query:
```sql
SELECT * FROM orders WHERE user_id = 123 ORDER BY created_at DESC;
```

**Tasks:**
1. Use EXPLAIN to diagnose
2. Create the proper index
3. Optimize the PHP query (N+1 prevention)
4. Add pagination

### Problem 3: Race Condition
**Scenario:** Two users try to purchase the last item in inventory simultaneously. Both see it as available. Both succeed.

**Tasks:**
1. Explain what happened
2. Implement a solution using database transactions
3. Add pessimistic locking
4. Handle the "sold out" case gracefully

### Problem 4: Server Overload
**Scenario:** Your PHP-FPM server has 4GB RAM but frequently runs out of memory during peak traffic.

**Tasks:**
1. Calculate optimal pm.max_children (assuming 64MB per process)
2. Tune the PHP-FPM configuration
3. Add monitoring to detect issues early
4. Implement a queue for heavy tasks

### Problem 5: Geographic Latency
**Scenario:** Your US-based server serves users worldwide. European users report 3-second load times.

**Tasks:**
1. Identify the likely bottleneck
2. Propose CDN solutions
3. Design a multi-region deployment
4. Calculate the cost-benefit of adding a European server

---

## 13.4 Capstone Assignment

### Build: Complete PHP Application Server

Build a production-ready PHP application server that demonstrates understanding of ALL Level 0 concepts:

**Requirements:**

1. **Server Architecture**
   - Nginx as reverse proxy
   - PHP-FPM as application server
   - MySQL or PostgreSQL as database
   - Redis for caching

2. **Application Features**
   - User registration and login (session-based)
   - RESTful API with proper HTTP semantics
   - CRUD operations on a resource (e.g., blog posts)
   - File upload with validation
   - Paginated listing
   - Search functionality

3. **System Features**
   - Proper directory structure
   - Composer autoloading (PSR-4)
   - Environment configuration (.env)
   - Error handling and logging
   - Input validation
   - Output escaping
   - SQL injection prevention (prepared statements)
   - CSRF protection
   - Rate limiting

4. **Performance**
   - OPcache enabled
   - Database indexing
   - Query optimization
   - Redis caching for frequent queries

5. **Deployment**
   - Docker Compose setup
   - Production Dockerfile
   - CI/CD pipeline configuration
   - Health check endpoint

6. **Documentation**
   - README with setup instructions
   - API documentation
   - Architecture diagram
   - Deployment guide

**Deliverables:**
- Complete source code in a Git repository
- Docker Compose configuration
- Database migration scripts
- Postman/Insomnia API collection
- Architecture documentation

**Evaluation Criteria:**
- Code quality and organization
- Security practices
- Performance optimization
- Error handling
- Documentation quality
- Understanding demonstrated in code

---

## Answer Key

### Quiz Answers
1. b) Executing instructions
2. c) RAM
3. b) Domain Name System
4. c) GET
5. b) 443
6. b) 192.168.1.1
7. a) Transmission Control Protocol
8. c) SSD
9. a) A program that manages computer hardware and software
10. c) 404
11. b) Saving and loading process states when switching between processes
12. b) To manage PHP worker processes for handling web requests
13. a) Atomicity, Consistency, Isolation, Durability
14. b) Speeding up data retrieval in databases
15. a) Model-View-Controller
16. b) Processes have isolated memory, threads share memory within a process
17. b) It maps virtual addresses to physical RAM using page tables
18. a) Running N+1 queries instead of optimizing with JOINs
19. b) It stores compiled PHP opcodes in shared memory
20. a) HTTP/2 is faster and supports multiplexing

---

*End of Chapter 13. Proceed to Chapter 14: Projects.*
