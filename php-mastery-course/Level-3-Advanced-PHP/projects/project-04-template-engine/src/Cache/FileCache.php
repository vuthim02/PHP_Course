<?php

declare(strict_types=1);

namespace TemplateEngine\Cache;

class FileCache
{
    private string $cachePath;

    public function __construct(?string $cachePath = null)
    {
        $this->cachePath = $cachePath ?? sys_get_temp_dir() . '/template-cache';
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function getCachePath(string $templateName): string
    {
        return $this->cachePath . '/' . md5($templateName) . '.php';
    }

    public function has(string $templateName, int $mtime = 0): bool
    {
        $path = $this->getCachePath($templateName);
        return file_exists($path) && ($mtime === 0 || filemtime($path) >= $mtime);
    }

    public function get(string $templateName): ?string
    {
        $path = $this->getCachePath($templateName);
        return file_exists($path) ? file_get_contents($path) : null;
    }

    public function set(string $templateName, string $content): void
    {
        file_put_contents($this->getCachePath($templateName), $content, LOCK_EX);
    }

    public function clear(): void
    {
        $files = glob($this->cachePath . '/*.php');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function getCacheDirectory(): string
    {
        return $this->cachePath;
    }
}
