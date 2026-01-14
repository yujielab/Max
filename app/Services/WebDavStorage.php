<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class WebDavStorage implements StorageGateway
{
    public function __construct(private readonly array $config)
    {
    }

    public function list(string $path): array
    {
        return [
            'provider' => 'webdav',
            'path' => $path,
            'items' => [],
        ];
    }

    public function createFolder(string $path, string $name): array
    {
        return [
            'provider' => 'webdav',
            'path' => $path,
            'name' => $name,
        ];
    }

    public function upload(string $path, UploadedFile $file): array
    {
        return [
            'provider' => 'webdav',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];
    }
}
