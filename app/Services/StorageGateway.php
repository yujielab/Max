<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface StorageGateway
{
    public function list(string $path): array;

    public function createFolder(string $path, string $name): array;

    public function upload(string $path, UploadedFile $file): array;
}
