# Chapter 16: File Operations

## Learning Objectives

- Read and write files
- Handle file uploads securely
- Work with directories
- Use file permissions

---

## 16.1 Reading Files

```php
<?php
// Read entire file into string
$content = file_get_contents('data.txt');
$json = file_get_contents('https://api.example.com/data');  // URL

// Read file into array (one line per element)
$lines = file('data.txt');  // Each line includes \n

// Read with stream (for large files)
$handle = fopen('large_file.txt', 'r');
while (($line = fgets($handle)) !== false) {
    processLine($line);  // Memory efficient
}
fclose($handle);

// CSV parsing
$handle = fopen('data.csv', 'r');
while (($row = fgetcsv($handle)) !== false) {
    // $row is an array of CSV fields
}
fclose($handle);
```

---

## 16.2 Writing Files

```php
<?php
// Write entire string
file_put_contents('data.txt', "Hello, World!\n");

// Append
file_put_contents('log.txt', "Error occurred\n", FILE_APPEND);

// Locking for concurrent access
file_put_contents('counter.txt', 1, LOCK_EX);

// Stream writing
$handle = fopen('output.txt', 'w');
fwrite($handle, "Line 1\n");
fwrite($handle, "Line 2\n");
fclose($handle);
```

---

## 16.3 File Uploads

```php
<?php
// HTML form
// <form method="POST" enctype="multipart/form-data">
//     <input type="file" name="document">
// </form>

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    
    // Validate
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $maxSize = 5 * 1024 * 1024;  // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed');
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new RuntimeException('Invalid file type');
    }
    
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('File too large');
    }
    
    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = bin2hex(random_bytes(16)) . '.' . $extension;
    
    // Move to storage
    move_uploaded_file($file['tmp_name'], "/uploads/{$newName}");
}
```

---

## 16.4 Exercises

1. Read a CSV file and parse it into an array
2. Write a log file with timestamps
3. Handle file upload with validation
4. Recursively list all files in a directory
5. Implement a simple file-based cache

---

## Further Reading

- **Doc:** [File System Functions](https://www.php.net/manual/en/ref.filesystem.php)
- **Doc:** [File Uploads](https://www.php.net/manual/en/features.file-upload.php)
