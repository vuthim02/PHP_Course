# Chapter 19: File Uploads and Processing

## Learning Objectives

- Handle file uploads securely
- Validate file types and sizes
- Process images with GD and Imagick
- Store files efficiently

---

## 19.1 Secure File Upload

```php
<?php
class FileUploader
{
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
    ];

    private const MAX_FILE_SIZE = 10_485_760; // 10MB

    public function __construct(
        private string $uploadDir = '/var/www/uploads'
    ) {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): string
    {
        $this->validate($file);
        
        $extension = self::ALLOWED_TYPES[$file['type']];
        $filename = $this->generateFilename($extension);
        $path = "{$this->uploadDir}/{$filename}";
        
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            throw new UploadException('Failed to move uploaded file');
        }

        return $filename;
    }

    private function validate(array $file): void
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException($this->getErrorMessage($file['error']));
        }

        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new UploadException(
                'File exceeds maximum size of ' . (self::MAX_FILE_SIZE / 1048576) . 'MB'
            );
        }

        // Validate MIME type (check both extension and content)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            throw new UploadException('File type not allowed');
        }

        // Verify it's not a script disguised as an image
        if (str_starts_with($mimeType, 'image/')) {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                throw new UploadException('Invalid image file');
            }
        }
    }

    private function generateFilename(string $extension): string
    {
        return bin2hex(random_bytes(16)) . '.' . $extension;
    }

    private function getErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
            UPLOAD_ERR_PARTIAL => 'File was partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temp directory',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            default => 'Unknown upload error',
        };
    }
}
```

---

## 19.2 Image Processing

```php
<?php
class ImageProcessor
{
    public function resize(string $path, int $width, int $height): string
    {
        $image = new Imagick($path);
        $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
        $image->writeImage($path);
        return $path;
    }

    public function createThumbnail(string $source, int $size = 150): string
    {
        $thumbPath = str_replace(
            pathinfo($source, PATHINFO_FILENAME),
            pathinfo($source, PATHINFO_FILENAME) . "_thumb",
            $source
        );

        $image = new Imagick($source);
        $image->cropThumbnailImage($size, $size);
        $image->writeImage($thumbPath);
        
        return $thumbPath;
    }

    public function optimize(string $path, int $quality = 85): void
    {
        $image = new Imagick($path);
        $image->setImageCompression(Imagick::COMPRESSION_JPEG);
        $image->setImageCompressionQuality($quality);
        $image->stripImage(); // Remove metadata
        $image->writeImage($path);
    }

    public function watermark(string $path, string $watermarkPath): string
    {
        $image = new Imagick($path);
        $watermark = new Imagick($watermarkPath);
        
        $watermark->resizeImage(
            $image->getImageWidth() * 0.3,
            0,
            Imagick::FILTER_LANCZOS,
            1
        );

        $x = $image->getImageWidth() - $watermark->getImageWidth() - 20;
        $y = $image->getImageHeight() - $watermark->getImageHeight() - 20;
        
        $image->compositeImage(
            $watermark,
            Imagick::COMPOSITE_OVER,
            $x,
            $y
        );
        
        $image->writeImage($path);
        return $path;
    }
}
```

---

## 19.3 Exercises

1. Create a secure file upload handler for PDF and image files
2. Implement image resizing and thumbnail generation
3. Add EXIF data stripping for uploaded images
4. Build a gallery with lazy loading and CDN integration

---

## Further Reading

- **Doc:** [PHP File Uploads](https://www.php.net/manual/en/features.file-upload.php)
- **Doc:** [Imagick Documentation](https://www.php.net/manual/en/book.imagick.php)
- **Doc:** [GD Documentation](https://www.php.net/manual/en/book.image.php)
