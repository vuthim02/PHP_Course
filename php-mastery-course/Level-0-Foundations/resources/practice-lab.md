# Level 0 — Practice Lab

## Lab 1: Set Up Your Development Environment

### Steps
1. Install PHP 8.2+ on your machine
2. Install Composer globally
3. Install a MySQL/MariaDB server
4. Install Git and configure your name/email
5. Install VS Code (or PHPStorm) with PHP extensions
6. Verify everything works:
   ```bash
   php -v            # Should show 8.2+
   composer --version
   mysql --version
   git --version
   ```

### Checkpoints
- [ ] Can run `php -S localhost:8000` and see a PHP page in the browser
- [ ] Can connect to MySQL with `mysql -u root -p`
- [ ] Can clone a repository with `git clone`

---

## Lab 2: Linux CLI Challenge

Complete these tasks using only the terminal:

1. Create this directory tree:
   ```
   projects/
   ├── blog/
   │   ├── public/
   │   └── src/
   └── api/
       └── tests/
   ```

2. Create a file `hello.txt` with "Hello Linux" as content

3. Find all `.php` files in a directory (use `find`)

4. Count how many lines are in all `.md` files combined

5. Search for the word "function" in all `.php` files

6. Download a file from the internet using `curl`

7. Run a process in the background and bring it back to foreground

---

## Lab 3: Git Challenge

```bash
# 1. Create a repo
mkdir git-lab && cd git-lab && git init

# 2. Make your first commit
echo "# Git Lab" > README.md
git add README.md
git commit -m "Initial commit"

# 3. Branch and merge
git checkout -b feature
echo "New feature" > feature.txt
git add feature.txt && git commit -m "Add feature"
git checkout main
git merge feature

# 4. Simulate a conflict
echo "Line from main" > conflict.txt
git add conflict.txt && git commit -m "Main change"
git checkout -b conflict-branch
echo "Line from branch" > conflict.txt
git add conflict.txt && git commit -m "Branch change"
git checkout main
git merge conflict-branch  # Conflict! Resolve it manually

# 5. Undo a mistake
echo "Mistake" > mistake.txt
git add mistake.txt && git commit -m "Oops"
git revert HEAD --no-edit   # Safe undo

# 6. Stash work
echo "WIP" > wip.txt
git stash
echo "Do other work" > done.txt
git add done.txt && git commit -m "Other work"
git stash pop
```

---

## Lab 4: HTTP Challenge

Using `curl` (or a browser's dev tools):

1. Send a GET request to `https://jsonplaceholder.typicode.com/posts/1`
2. Send a POST request to the same URL with `{"title": "foo", "body": "bar"}`
3. Inspect the response headers (especially `Content-Type`, `Cache-Control`)
4. Follow a redirect: `curl -L http://httpbin.org/redirect-to?url=https://example.com`
5. Check the response time: `curl -w "%{time_total}\n" https://example.com`

---

## Lab 5: Database Challenge

```sql
-- 1. Create a database
CREATE DATABASE shop;

-- 2. Create a table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Insert data
INSERT INTO products (name, price) VALUES
    ('Laptop', 999.99),
    ('Mouse', 19.99),
    ('Keyboard', 49.99);

-- 4. Query data
SELECT * FROM products WHERE price > 50;
SELECT COUNT(*) FROM products;

-- 5. Update and delete
UPDATE products SET price = 899.99 WHERE name = 'Laptop';
DELETE FROM products WHERE price < 20;
```
