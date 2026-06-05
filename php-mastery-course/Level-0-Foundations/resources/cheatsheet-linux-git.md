# Linux & Git Cheat Sheet

## Linux CLI — Essentials

```bash
# Navigation
pwd                       # Print working directory
ls -la                    # List files (including hidden, with details)
cd /path/to/dir           # Change directory
cd ~                      # Go home
cd -                      # Go to previous directory
mkdir -p a/b/c            # Create nested directories

# File Operations
cp file.txt /dest/        # Copy
mv file.txt /dest/        # Move / rename
rm file.txt               # Delete file
rm -rf dir/               # Delete directory (⚠️ dangerous)
touch file.txt            # Create empty file / update timestamp
cat file.txt              # Print file to terminal
less file.txt             # View file with scrolling (q to quit)
head -n 20 file.txt       # First 20 lines
tail -f file.txt          # Follow file as it grows (logs)

# Permissions
chmod +x script.sh        # Make executable
chmod 644 file.txt        # rw-r--r--
chmod 755 script.sh       # rwxr-xr-x
chown user:group file     # Change owner/group

# Processes
ps aux                    # List all processes
top / htop                # Interactive process viewer
kill -9 PID               # Force kill process
kill -15 PID              # Graceful termination

# Text Processing
grep "pattern" file.txt   # Search for pattern
grep -r "pattern" .       # Recursive search
wc -l file.txt            # Count lines
sort file.txt             # Sort lines
uniq                      # Deduplicate adjacent lines
sed 's/old/new/g' file    # Find and replace
awk '{print $1}' file     # Print first column

# Network
curl https://example.com  # HTTP GET request
ping google.com           # Test connectivity
netstat -tlnp             # Show listening ports
ss -tlnp                  # Modern netstat alternative
ifconfig / ip addr        # Show network interfaces
```

## Git — Essentials

```bash
# Setup
git config --global user.name "Your Name"
git config --global user.email "you@example.com"
git config --global init.defaultBranch main

# Daily Workflow
git clone <url>                     # Clone a repository
git status                          # See what's changed
git add file.txt                    # Stage a file
git add .                           # Stage all changes
git commit -m "message"             # Commit staged changes
git push                            # Send commits to remote
git pull                            # Fetch + merge remote changes

# Branching
git branch                          # List branches
git branch feature-x                # Create new branch
git checkout feature-x              # Switch to branch
git checkout -b feature-x           # Create + switch
git merge feature-x                 # Merge into current branch
git branch -d feature-x             # Delete branch (local)
git push origin --delete feature-x  # Delete branch (remote)

# History
git log --oneline                   # Compact history
git log --graph --oneline --all     # Visual tree
git diff                            # Unstaged changes
git diff --staged                   # Staged changes
git show COMMIT_HASH                # View specific commit

# Undoing
git restore file.txt                # Discard unstaged changes
git restore --staged file.txt       # Unstage
git reset --soft HEAD~1             # Undo last commit, keep changes
git reset --hard HEAD~1             # Undo last commit, discard changes
git revert COMMIT_HASH              # Create inverse commit (safe for shared branches)

# Stashing
git stash                           # Save uncommitted changes
git stash pop                       # Restore and remove stash
git stash list                      # View all stashes
git stash drop                      # Delete stash

# Remotes
git remote -v                       # List remotes
git remote add origin <url>         # Add remote
git fetch origin                    # Get remote changes without merging
git rebase origin/main              # Rebase current branch onto main

# Advanced
git bisect                          # Binary search for bug-introducing commit
git cherry-pick COMMIT_HASH         # Apply specific commit to current branch
git tag v1.0.0                      # Tag a commit
git stash apply stash@{2}           # Apply specific stash
```

## Common Problem Solving

```bash
# "I committed to the wrong branch"
git log --oneline -1           # Copy the commit hash
git checkout correct-branch
git cherry-pick COMMIT_HASH
git checkout wrong-branch
git reset --hard HEAD~1        # Remove from wrong branch

# "I need to fix the last commit message"
git commit --amend -m "New message"

# "I accidentally deleted a file"
git checkout -- file.txt       # Restore from git

# "My branch is behind main"
git checkout main
git pull
git checkout my-branch
git rebase main                # Or: git merge main
```
