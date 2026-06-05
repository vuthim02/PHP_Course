# Chapter 23: Cloud File Storage

## Learning Objectives

By the end of this chapter you will:
- Integrate cloud storage (S3, GCS, Azure) with PHP
- Upload files directly from browsers to cloud storage
- Generate signed URLs for secure temporary access
- Serve files through CDN for global performance
- Implement image transformations in the cloud
- Manage storage costs and lifecycle policies

---

## 23.1 Why Cloud Storage?

Storing files on the application server has problems:
- Server disk fills up
- Files lost on server termination
- Slow global access (single datacenter)
- Backup complexity
- Scaling requires file migration

Cloud storage solves these by providing:

| Feature | Benefit |
|---------|---------|
| **Global CDN** | Files served from edge locations near users |
| **Automatic scaling** | No disk space worries |
| **Durability** | 99.999999999% durability (11 9s) for S3 |
| **Lifecycle policies** | Auto-move old files to cheaper storage |
| **Signed URLs** | Temporary secure access without authentication |
| **Image processing** | Resize, crop, format conversion on the fly |

### Providers Comparison

| Provider | PHP SDK | Free Tier | Global Regions |
|----------|---------|-----------|----------------|
| AWS S3 | `aws/aws-sdk-php` | 5GB free, 12 months | 100+ |
| Google Cloud Storage | `google/cloud-storage` | 5GB free, $300 credit | 100+ |
| Azure Blob Storage | `microsoft/azure-storage-blob` | 5GB free | 60+ |
| DigitalOcean Spaces | S3-compatible API | 250GB free | 10+ |
| MinIO (Self-hosted) | S3-compatible API | Unlimited | Your own |

---

## 23.2 AWS S3 Integration

### Setup

```bash
composer require aws/aws-sdk-php
```

```php
<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Storage
{
    private S3Client $client;
    private string $bucket;

    public function __construct()
    {
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => getenv('AWS_REGION') ?: 'us-east-1',
            'credentials' => [
                'key' => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $this->bucket = getenv('AWS_BUCKET') ?: 'myapp-uploads';
    }

    public function upload(string $key, $body, array $options = []): string
    {
        $params = [
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
            'ContentType' => $options['content_type'] ?? null,
            'ACL' => $options['acl'] ?? 'private',
        ];

        // Remove null values
        $params = array_filter($params, fn($v) => $v !== null);

        $this->client->putObject($params);

        return $key;
    }

    public function uploadFromPath(string $key, string $filePath, array $options = []): string
    {
        return $this->upload($key, fopen($filePath, 'rb'), $options);
    }

    public function uploadFromString(string $key, string $content, array $options = []): string
    {
        return $this->upload($key, $content, $options);
    }

    public function download(string $key, string $savePath = null): string
    {
        $result = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'SaveAs' => $savePath,
        ]);

        return $savePath ?? (string) $result['Body'];
    }

    public function delete(string $key): bool
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return true;
    }

    public function exists(string $key): bool
    {
        return $this->client->doesObjectExist($this->bucket, $key);
    }

    public function listObjects(string $prefix = ''): array
    {
        $objects = [];
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => $prefix,
        ]);

        foreach ($result['Contents'] ?? [] as $object) {
            $objects[] = [
                'key' => $object['Key'],
                'size' => $object['Size'],
                'last_modified' => $object['LastModified'],
                'etag' => $object['ETag'],
            ];
        }

        return $objects;
    }

    public function getUrl(string $key): string
    {
        return "https://{$this->bucket}.s3.{$this->client->getRegion()}.amazonaws.com/{$key}";
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }
}
```

### Usage

```php
<?php
$storage = new S3Storage();

// Upload a file
$key = "users/{$userId}/profile-{$timestamp}.jpg";
$storage->uploadFromPath($key, '/tmp/upload.jpg', [
    'content_type' => 'image/jpeg',
    'acl' => 'public-read', // For public images
]);

// Upload JSON data
$storage->uploadFromString(
    "exports/users-2024-01.json",
    json_encode($users),
    ['content_type' => 'application/json']
);

// Download
$storage->download("backups/db-2024-01.sql", '/tmp/db-restore.sql');

// List user's files
$files = $storage->listObjects("users/{$userId}/");
```

---

## 23.3 Signed URLs (Temporary Access)

Signed URLs let you grant temporary access to private files without making them public:

```php
<?php
use Aws\CommandPool;
use Aws\S3\S3Client;

class SignedUrlGenerator
{
    private S3Client $client;
    private string $bucket;

    public function __construct(S3Client $client, string $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    // Get a temporary download URL (expires automatically)
    public function getDownloadUrl(string $key, int $expiresInSeconds = 3600): string
    {
        $command = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        $request = $this->client->createPresignedRequest($command, "+{$expiresInSeconds} seconds");

        return (string) $request->getUri();
    }

    // Get a temporary upload URL (browser uploads directly to S3)
    public function getUploadUrl(string $key, string $contentType, int $expiresInSeconds = 3600): string
    {
        $command = $this->client->getCommand('PutObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        $request = $this->client->createPresignedRequest($command, "+{$expiresInSeconds} seconds");

        return (string) $request->getUri();
    }

    // Generate thumbnail URL on the fly (requires S3 Object Lambda or CDN)
    public function getThumbnailUrl(string $key, int $width = 200): string
    {
        // With S3 + CloudFront + Lambda@Edge
        return "https://cdn.example.com/thumbnails/{$width}/{$key}";
    }

    // Generate multiple URLs efficiently
    public function getBulkDownloadUrls(array $keys, int $expiresInSeconds = 3600): array
    {
        $commands = [];
        foreach ($keys as $key) {
            $commands[] = $this->client->getCommand('GetObject', [
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
        }

        $urls = [];
        $pool = new CommandPool($this->client, $commands, [
            'concurrency' => 10,
            'fulfilled' => function ($result, $iterKey) use (&$urls, $expiresInSeconds) {
                $request = $this->client->createPresignedRequest($result, "+{$expiresInSeconds} seconds");
                $urls[$iterKey] = (string) $request->getUri();
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        return $urls;
    }
}

// Usage
$urlGen = new SignedUrlGenerator($s3Client, 'myapp-files');

// Protected document — user has 1 hour to view it
$downloadUrl = $urlGen->getDownloadUrl('documents/contract-42.pdf', 3600);
echo "<a href=\"{$downloadUrl}\">Download Contract</a>";

// Direct browser upload — user uploads directly to S3, bypassing your server
$uploadUrl = $urlGen->getUploadUrl('uploads/photo.jpg', 'image/jpeg', 1800);
```

### Direct Browser Upload (Presigned POST)

```php
<?php
// Generate presigned POST data for direct browser uploads
$result = $s3Client->createPresignedPost([
    'Bucket' => $this->bucket,
    'Key' => 'uploads/${filename}',
    'Expires' => time() + 3600,
    'Conditions' => [
        ['bucket' => $this->bucket],
        ['starts-with', '$key', 'uploads/'],
        ['content-length-range', 1, 10 * 1024 * 1024], // Max 10MB
        ['starts-with', '$Content-Type', 'image/'],
    ],
]);

// Return these to the frontend
echo json_encode([
    'url' => $result['url'],
    'fields' => $result['fields'],
]);

// Frontend (JavaScript):
// const formData = new FormData();
// Object.entries(fields).forEach(([k, v]) => formData.append(k, v));
// formData.append('file', fileInput.files[0]);
// fetch(url, { method: 'POST', body: formData });
```

---

## 23.4 CDN Integration

```php
<?php
/**
 * CDN-managed file serving
 */
class CdnManager
{
    private string $cdnDomain;
    private S3Storage $storage;
    private SignedUrlGenerator $urlGen;

    public function __construct(string $cdnDomain, S3Storage $storage, SignedUrlGenerator $urlGen)
    {
        $this->cdnDomain = rtrim($cdnDomain, '/');
        $this->storage = $storage;
        $this->urlGen = $urlGen;
    }

    // Public file: served through CDN directly
    public function publicUrl(string $key): string
    {
        return "{$this->cdnDomain}/{$key}";
    }

    // Private file: served through CDN with signed cookie
    public function privateUrl(string $key, int $expiresInSeconds = 3600): string
    {
        return $this->urlGen->getDownloadUrl($key, $expiresInSeconds);
    }

    // Invalidate CDN cache for a file
    public function invalidate(string $key): void
    {
        // Requires CloudFront or similar API
        // $cloudFront->createInvalidation([
        //     'DistributionId' => $distributionId,
        //     'InvalidationBatch' => [
        //         'Paths' => ['Items' => ["/{$key}"], 'Quantity' => 1],
        //         'CallerReference' => uniqid(),
        //     ],
        // ]);
    }

    // Get CDN URL with image transformation (if supported)
    public function transformedUrl(string $key, array $transformations): string
    {
        // With imgix or Cloudinary
        $params = http_build_query($transformations);
        return "{$this->cdnDomain}/{$key}?{$params}";
    }
}

// Usage
$cdn = new CdnManager('https://cdn.myapp.com', $storage, $urlGen);

echo $cdn->publicUrl('images/logo.png');
// https://cdn.myapp.com/images/logo.png

echo $cdn->transformedUrl('images/photo.jpg', [
    'w' => 400,
    'h' => 300,
    'fit' => 'crop',
    'auto' => 'format',
]);
// https://cdn.myapp.com/images/photo.jpg?w=400&h=300&fit=crop&auto=format
```

---

## 23.5 File Upload Controller

```php
<?php
class FileUploadController
{
    private S3Storage $storage;
    private array $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
        'text/csv' => 'csv',
    ];
    private int $maxSize = 10 * 1024 * 1024; // 10MB

    public function __construct(S3Storage $storage)
    {
        $this->storage = $storage;
    }

    public function upload(array $file, int $userId): array
    {
        // Validate
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!isset($this->allowedMimes[$mime])) {
            throw new \InvalidArgumentException('File type not allowed');
        }

        if ($file['size'] > $this->maxSize) {
            throw new \InvalidArgumentException('File too large');
        }

        // Generate unique key
        $extension = $this->allowedMimes[$mime];
        $key = "users/{$userId}/uploads/" . bin2hex(random_bytes(16)) . ".{$extension}";

        // Upload to S3
        $this->storage->upload($key, fopen($file['tmp_name'], 'rb'), [
            'content_type' => $mime,
            'acl' => 'private',
        ]);

        // Return file metadata
        return [
            'key' => $key,
            'mime' => $mime,
            'size' => $file['size'],
            'uploaded_at' => date('c'),
        ];
    }

    public function delete(string $key, int $userId): void
    {
        // Ensure user owns the file (security check)
        if (!str_starts_with($key, "users/{$userId}/")) {
            throw new \RuntimeException('Access denied');
        }

        $this->storage->delete($key);
    }
}
```

---

## 23.6 Lifecycle Policies

```php
<?php
/**
 * Configure S3 lifecycle rules via PHP SDK
 */
class LifecycleManager
{
    private S3Client $client;
    private string $bucket;

    public function __construct(S3Client $client, string $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    // Move old files to cheaper storage tiers
    public function setLifecyclePolicy(): void
    {
        $this->client->putBucketLifecycleConfiguration([
            'Bucket' => $this->bucket,
            'LifecycleConfiguration' => [
                'Rules' => [
                    [
                        'ID' => 'expire-logs',
                        'Status' => 'Enabled',
                        'Prefix' => 'logs/',
                        'Expiration' => ['Days' => 90],
                    ],
                    [
                        'ID' => 'archive-old-uploads',
                        'Status' => 'Enabled',
                        'Prefix' => 'uploads/',
                        'Transitions' => [
                            [
                                'Days' => 30,
                                'StorageClass' => 'STANDARD_IA', // Infrequent Access
                            ],
                            [
                                'Days' => 90,
                                'StorageClass' => 'GLACIER', // Archived
                            ],
                            [
                                'Days' => 365,
                                'StorageClass' => 'DEEP_ARCHIVE', // Cheapest
                            ],
                        ],
                    ],
                    [
                        'ID' => 'delete-temp-files',
                        'Status' => 'Enabled',
                        'Prefix' => 'temp/',
                        'Expiration' => ['Days' => 1],
                    ],
                ],
            ],
        ]);
    }

    // Get current storage usage
    public function getStorageUsage(): array
    {
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
        ]);

        $totalSize = 0;
        $count = 0;
        $byPrefix = [];

        foreach ($result['Contents'] ?? [] as $object) {
            $totalSize += $object['Size'];
            $count++;
            $prefix = explode('/', $object['Key'])[0];
            $byPrefix[$prefix] = ($byPrefix[$prefix] ?? 0) + $object['Size'];
        }

        return [
            'total_objects' => $count,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'by_prefix' => array_map(fn($s) => $this->formatBytes($s), $byPrefix),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
```

---

## 23.7 Working with Google Cloud Storage

```bash
composer require google/cloud-storage
```

```php
<?php
use Google\Cloud\Storage\StorageClient;

class GcsStorage
{
    private StorageClient $client;
    private string $bucket;

    public function __construct()
    {
        $this->client = new StorageClient([
            'projectId' => getenv('GCP_PROJECT_ID'),
            'keyFilePath' => getenv('GCP_KEY_FILE'),
        ]);

        $this->bucket = getenv('GCS_BUCKET') ?: 'myapp-uploads';
    }

    public function upload(string $key, string $sourcePath): string
    {
        $bucket = $this->client->bucket($this->bucket);
        $bucket->upload(fopen($sourcePath, 'r'), [
            'name' => $key,
            'metadata' => ['contentType' => mime_content_type($sourcePath)],
        ]);

        return $key;
    }

    public function getSignedUrl(string $key, int $expiresInMinutes = 60): string
    {
        $bucket = $this->client->bucket($this->bucket);
        $object = $bucket->object($key);

        return $object->signedUrl(
            new \DateTime("+{$expiresInMinutes} minutes"),
            [
                'version' => 'v4',
            ]
        );
    }

    public function makePublic(string $key): void
    {
        $bucket = $this->client->bucket($this->bucket);
        $object = $bucket->object($key);
        $object->update(['acl' => []], ['predefinedAcl' => 'PUBLIC_READ']);
    }

    public function delete(string $key): void
    {
        $bucket = $this->client->bucket($this->bucket);
        $object = $bucket->object($key);
        $object->delete();
    }
}
```

---

## 23.8 File Validation and Security

```php
<?php
class FileValidator
{
    // Validate by magic bytes (not just extension)
    public function validateSignature(string $filePath): ?string
    {
        $handle = fopen($filePath, 'rb');
        $header = fread($handle, 8);
        fclose($handle);

        $signatures = [
            "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A" => 'image/png',
            "\xFF\xD8" => 'image/jpeg',
            "\x47\x49\x46\x38\x37\x61" => 'image/gif',
            "\x47\x49\x46\x38\x39\x61" => 'image/gif',
            "\x25\x50\x44\x46" => 'application/pdf',
            "\x50\x4B\x03\x04" => 'application/zip',
        ];

        foreach ($signatures as $sig => $mime) {
            if (str_starts_with($header, $sig)) {
                return $mime;
            }
        }

        return null;
    }

    // Validate image integrity
    public function validateImage(string $filePath): bool
    {
        $info = @getimagesize($filePath);
        if ($info === false) return false;

        // Check for embedded PHP code
        $content = file_get_contents($filePath, false, null, 0, 1024);
        if (stripos($content, '<?php') !== false) return false;

        return true;
    }

    // Scan for malware signatures (basic)
    public function basicMalwareScan(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        $suspicious = [
            '<?php system(',
            '<?php exec(',
            '<?php shell_exec(',
            '<?php passthru(',
            '<?php eval(',
            '<?php base64_decode(',
        ];

        foreach ($suspicious as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return false;
            }
        }

        return true;
    }
}
```

---

## 23.9 Exercises

1. **S3 upload:** Upload a file to S3 and retrieve it
2. **Signed URLs:** Generate a signed URL that expires in 5 minutes
3. **Direct browser upload:** Implement presigned POST uploads
4. **CDN:** Serve files through a CDN and measure latency difference
5. **Lifecycle policies:** Configure automatic archiving for files older than 30 days
6. **Image optimization:** Automatically convert uploaded images to WebP format

---

## 23.10 Interview Questions

1. "How does cloud storage differ from local server storage?"
2. "What are signed URLs and when would you use them?"
3. "How would you handle direct browser uploads to S3 without exposing credentials?"
4. "Explain S3 storage tiers and when to use each."
5. "How do you invalidate cached files on a CDN?"
6. "What security checks should you perform on uploaded files?"
7. "How would you migrate terabytes of existing files to cloud storage?"

---

## Further Reading

- **SDK:** [AWS SDK for PHP](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/)
- **SDK:** [Google Cloud Storage PHP](https://cloud.google.com/php/docs/reference/cloud-storage/latest)
- **Best Practices:** [S3 Security Best Practices](https://docs.aws.amazon.com/AmazonS3/latest/userguide/security-best-practices.html)
- **Tool:** [MinIO](https://min.io/) — Self-hosted S3-compatible storage

---

*End of Chapter 23. Proceed to Chapter 24: Push Notifications.*
