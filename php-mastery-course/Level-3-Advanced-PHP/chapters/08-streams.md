# Chapter 8: Streams

## Learning Objectives

- Understand PHP stream wrappers
- Read and write streams
- Use stream contexts and filters
- Create custom stream wrappers

---

## 8.1 Stream Basics

```php
<?php
// Different stream wrappers
$sources = [
    'file://' => fopen('/etc/passwd', 'r'),
    'http://' => fopen('https://api.example.com/data.json', 'r'),
    'php://' => fopen('php://memory', 'r+'),
    'php://input' => fopen('php://input', 'r'),
    'php://output' => fopen('php://output', 'w'),
    'data://' => fopen('data://text/plain;base64,' . base64_encode('Hello'), 'r'),
];

// Stream metadata
$stream = fopen('php://memory', 'r+');
fwrite($stream, 'Hello World');
rewind($stream);

$meta = stream_get_meta_data($stream);
var_dump($meta);
// [
//   'wrapper_type' => 'PHP',
//   'stream_type' => 'MEMORY',
//   'mode' => 'r+',
//   'unread_bytes' => 0,
//   'seekable' => true,
//   ...
// ]

// Stream contents
$content = stream_get_contents($stream); // Hello World
fclose($stream);
```

---

## 8.2 Stream Contexts

```php
<?php
// HTTP request with custom headers
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", [
            'Content-Type: application/json',
            'Authorization: Bearer my-token',
            'User-Agent: MyApp/1.0',
        ]),
        'content' => json_encode([
            'name' => 'John',
            'email' => 'john@example.com',
        ]),
        'ignore_errors' => true,
        'timeout' => 30,
    ],
    'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true,
    ],
]);

$result = file_get_contents('https://api.example.com/users', false, $context);

// FTP context
$ftpContext = stream_context_create([
    'ftp' => [
        'overwrite' => true,
        'resume_pos' => 0,
    ],
]);
```

---

## 8.3 Stream Filters

```php
<?php
// Register and apply filters
$stream = fopen('php://memory', 'r+');

// Apply base64 encode filter
stream_filter_append($stream, 'convert.base64-encode');
fwrite($stream, 'Hello World');
rewind($stream);
echo stream_get_contents($stream); // SGVsbG8gV29ybGQ=

// Custom stream filter
class UpperCaseFilter extends php_user_filter
{
    public function filter($in, $out, &$consumed, bool $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = strtoupper($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_FEED_ME;
    }
}

stream_filter_register('uppercase', UpperCaseFilter::class);

$stream = fopen('php://memory', 'r+');
stream_filter_append($stream, 'uppercase');
fwrite($stream, 'hello world');
rewind($stream);
echo stream_get_contents($stream); // HELLO WORLD
```

---

## 8.4 Exercises

1. Use streams to download a file from a URL with progress tracking
2. Create a custom stream wrapper for a virtual filesystem
3. Implement a stream filter that compresses data on-the-fly
4. Build a stream-based CSV parser that handles large files

---

## Further Reading

- **Doc:** [PHP Streams](https://www.php.net/manual/en/book.stream.php)
