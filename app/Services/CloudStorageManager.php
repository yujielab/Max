<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CloudStorageManager implements StorageGateway
{
    public function __construct(
        private readonly LocalStorage $local,
        private readonly WebDavStorage $webDav,
        private readonly S3Storage $s3,
        private readonly string $defaultDriver
    ) {
    }

    public function list(string $path): array
    {
        return $this->driver()->list($path);
    }

    public function createFolder(string $path, string $name): array
    {
        return $this->driver()->createFolder($path, $name);
    }

    public function upload(string $path, UploadedFile $file): array
    {
        return $this->driver()->upload($path, $file);
    }

    private function driver(): StorageGateway
    {
        return match ($this->defaultDriver) {
            'webdav' => $this->webDav,
            's3' => $this->s3,
            default => $this->local,
        };
    }
}
