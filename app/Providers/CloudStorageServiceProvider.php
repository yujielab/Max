<?php

namespace App\Providers;

use App\Services\CloudStorageManager;
use App\Services\S3Storage;
use App\Services\StorageGateway;
use App\Services\WebDavStorage;
use Illuminate\Support\ServiceProvider;

class CloudStorageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WebDavStorage::class, function () {
            return new WebDavStorage(config('icloud.webdav'));
        });

        $this->app->singleton(S3Storage::class, function () {
            return new S3Storage(config('icloud.s3'));
        });

        $this->app->singleton(CloudStorageManager::class, function ($app) {
            return new CloudStorageManager(
                $app->make(WebDavStorage::class),
                $app->make(S3Storage::class),
                config('icloud.default')
            );
        });

        $this->app->bind(StorageGateway::class, CloudStorageManager::class);
    }
}
