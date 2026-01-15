<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LocalStorage implements StorageGateway
{
    public function __construct(
        private readonly array $config,
        private readonly string $provider = 'local'
    )
    {
    }

    private ?FilesystemAdapter $filesystem = null;

    public function list(string $path): array
    {
        $relativePath = $this->resolvePath($path);
        $disk = $this->disk();

        $this->ensureDirectory($relativePath);

        $directories = $disk->directories($relativePath);
        $files = $disk->files($relativePath);

        $items = [];
        foreach ($directories as $directory) {
            $items[] = [
                'type' => 'folder',
                'name' => basename($directory),
                'path' => $this->displayPath($directory),
                'updated_at' => $this->formatTimestamp($disk->lastModified($directory)),
            ];
        }

        $totalSize = 0;
        foreach ($files as $file) {
            $size = $disk->size($file);
            $items[] = [
                'type' => 'file',
                'name' => basename($file),
                'path' => $this->displayPath($file),
                'size' => $size,
                'updated_at' => $this->formatTimestamp($disk->lastModified($file)),
            ];
            $totalSize += $size;
        }

        usort($items, function (array $left, array $right) {
            if ($left['type'] === $right['type']) {
                return $left['name'] <=> $right['name'];
            }

            return $left['type'] === 'folder' ? -1 : 1;
        });

        return [
            'provider' => $this->provider,
            'path' => $this->normalizeDisplayPath($path),
            'items' => $items,
            'stats' => [
                'folders' => count($directories),
                'files' => count($files),
                'size' => $totalSize,
            ],
        ];
    }

    public function createFolder(string $path, string $name): array
    {
        $relativePath = $this->resolvePath($path);
        $disk = $this->disk();

        $this->ensureDirectory($relativePath);

        $cleanName = $this->sanitizeName($name);
        $folderPath = rtrim($relativePath . '/' . $cleanName, '/');

        if (! $disk->exists($folderPath)) {
            $disk->makeDirectory($folderPath);
        }

        return [
            'provider' => $this->provider,
            'path' => $this->displayPath($folderPath),
            'name' => $cleanName,
        ];
    }

    public function upload(string $path, UploadedFile $file): array
    {
        $relativePath = $this->resolvePath($path);
        $disk = $this->disk();

        $this->ensureDirectory($relativePath);

        $originalName = $file->getClientOriginalName();
        $targetName = $this->uniqueFilename($relativePath, $originalName);
        $storedPath = $disk->putFileAs($relativePath, $file, $targetName);

        return [
            'provider' => $this->provider,
            'path' => $this->normalizeDisplayPath($path),
            'filename' => $targetName,
            'original_name' => $originalName,
            'stored_path' => $this->displayPath($storedPath),
            'size' => $file->getSize(),
        ];
    }

    private function disk()
    {
        if ($this->filesystem === null) {
            $this->filesystem = Storage::disk('local');
        }

        return $this->filesystem;
    }

    private function ensureDirectory(string $path): void
    {
        if ($path === '') {
            return;
        }

        $disk = $this->disk();
        if (! $disk->exists($path)) {
            $disk->makeDirectory($path);
        }
    }

    private function resolvePath(string $path): string
    {
        $root = trim((string) ($this->config['root'] ?? 'cloud-drive'), '/');
        $clean = $this->sanitizePath($path);

        if ($root === '') {
            return $clean;
        }

        return $clean === '' ? $root : $root . '/' . $clean;
    }

    private function sanitizePath(string $path): string
    {
        $normalized = str_replace('\\', '/', trim($path));
        $normalized = trim($normalized, '/');

        if ($normalized === '') {
            return '';
        }

        $segments = array_filter(explode('/', $normalized), function (string $segment) {
            return $segment !== '' && $segment !== '.' && $segment !== '..';
        });

        return implode('/', $segments);
    }

    private function sanitizeName(string $name): string
    {
        $clean = trim($name);
        $clean = str_replace(['\\', '/'], '', $clean);

        return $clean === '' ? '未命名文件夹' : $clean;
    }

    private function uniqueFilename(string $path, string $filename): string
    {
        $disk = $this->disk();
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extensionSuffix = $extension !== '' ? '.' . $extension : '';
        $candidate = $base . $extensionSuffix;
        $index = 1;

        while ($disk->exists($path . '/' . $candidate)) {
            $candidate = sprintf('%s-%d%s', $base, $index, $extensionSuffix);
            $index++;
        }

        return $candidate;
    }

    private function displayPath(?string $storedPath): string
    {
        if ($storedPath === null || $storedPath === '') {
            return '/';
        }

        $root = trim((string) ($this->config['root'] ?? 'cloud-drive'), '/');
        $normalized = str_replace('\\', '/', $storedPath);

        if ($root !== '' && str_starts_with($normalized, $root)) {
            $normalized = ltrim(substr($normalized, strlen($root)), '/');
        }

        return $normalized === '' ? '/' : '/' . $normalized;
    }

    private function normalizeDisplayPath(string $path): string
    {
        $clean = $this->sanitizePath($path);

        return $clean === '' ? '/' : '/' . $clean;
    }

    private function formatTimestamp(?int $timestamp): ?string
    {
        if ($timestamp === null) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp)->toIso8601String();
    }
}
