<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class S3Storage implements StorageGateway
{
    public function __construct(private readonly array $config)
    {
    }

    public function list(string $path): array
    {
        return [
            'provider' => 's3',
            'path' => $path,
            'items' => [],
        ];
    }

    public function createFolder(string $path, string $name): array
    {
        return [
            'provider' => 's3',
            'path' => $path,
            'name' => $name,
        ];
    }

    public function upload(string $path, UploadedFile $file): array
    {
        return [
            'provider' => 's3',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ];
    }
}
