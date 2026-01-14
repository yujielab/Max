<?php

namespace App\Services;

class S3Storage extends LocalStorage
{
    public function __construct(array $config)
    {
        $root = $config['root'] ?? 'cloud-drive/s3';

        parent::__construct(array_merge($config, ['root' => $root]), 's3');
    }
}
