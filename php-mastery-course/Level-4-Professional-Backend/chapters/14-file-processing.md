# Chapter 14: File Processing

## Learning Objectives

- Handle file uploads at scale
- Process images with GD and Imagick
- Integrate CDN for file delivery
- Build file processing pipelines

---

## 14.1 File Processing Pipeline

```php
<?php
namespace App\Media;

class FileProcessor
{
    public function __construct(
        private string $storagePath = '/var/www/storage'
    ) {}

    public function process(string $path, array $operations): array
    {
        $results = [];
        
        foreach ($operations as $operation) {
            $results[] = match ($operation['type']) {
                'resize' => $this->resize($path, $operation),
                'thumbnail' => $this->thumbnail($path, $operation),
                'optimize' => $this->optimize($path, $operation),
                'watermark' => $this->watermark($path, $operation),
                'convert' => $this->convert($path, $operation),
                default => throw new \InvalidArgumentException("Unknown operation: {$operation['type']}"),
            };
        }

        return $results;
    }

    private function resize(string $path, array $config): string
    {
        $image = new \Imagick($path);
        $image->resizeImage(
            $config['width'] ?? 800,
            $config['height'] ?? 600,
            \Imagick::FILTER_LANCZOS,
            1,
            true
        );
        
        $outputPath = $this->outputPath($path, "{$config['width']}x{$config['height']}");
        $image->writeImage($outputPath);
        return $outputPath;
    }

    private function thumbnail(string $path, array $config): string
    {
        $image = new \Imagick($path);
        $size = $config['size'] ?? 150;
        $image->cropThumbnailImage($size, $size);
        
        $outputPath = $this->outputPath($path, "thumb_{$size}");
        $image->writeImage($outputPath);
        return $outputPath;
    }

    private function optimize(string $path, array $config): string
    {
        $image = new \Imagick($path);
        $image->setImageCompressionQuality($config['quality'] ?? 80);
        $image->stripImage();
        $image->writeImage($path);
        return $path;
    }

    private function outputPath(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return "{$info['dirname']}/{$info['filename']}_{$suffix}.{$info['extension']}";
    }
}

// Async file processing job
class ProcessFileJob extends Job
{
    public function __construct(
        private string $path,
        private array $operations
    ) {}

    public function handle(): void
    {
        $processor = new FileProcessor();
        $results = $processor->process($this->path, $this->operations);
        
        // Store results in database
        foreach ($results as $result) {
            MediaRecord::create([
                'original_path' => $this->path,
                'processed_path' => $result,
                'status' => 'completed',
            ]);
        }
    }
}
```

---

## 14.2 Exercises

1. Build a file upload API that processes images asynchronously
2. Implement thumbnail generation for uploaded images
3. Add CDN integration (AWS S3, Cloudflare R2) for file delivery
4. Create a video transcoding pipeline using FFmpeg

---

## Further Reading

- **Doc:** [Imagick Documentation](https://www.php.net/manual/en/book.imagick.php)
- **Doc:** [AWS SDK PHP](https://docs.aws.amazon.com/sdk-for-php/)
