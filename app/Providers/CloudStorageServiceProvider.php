<?php

namespace App\Providers;

use App\Services\CloudStorageManager;
use App\Services\LocalStorage;
use App\Services\StorageGateway;
use Illuminate\Support\ServiceProvider;

class CloudStorageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LocalStorage::class, function () {
            return new LocalStorage(config('icloud.local'));
        });

        $this->app->singleton(CloudStorageManager::class, function ($app) {
            return new CloudStorageManager(
                $app->make(LocalStorage::class)
            );
        });

        $this->app->bind(StorageGateway::class, CloudStorageManager::class);
    }
}
