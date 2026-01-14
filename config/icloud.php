<?php

return [
    'default' => env('ICLOUD_STORAGE', 'local'),
    'local' => [
        'root' => env('ICLOUD_LOCAL_ROOT', 'cloud-drive'),
    ],
    'webdav' => [
        'base_uri' => env('WEBDAV_BASE_URI'),
        'username' => env('WEBDAV_USERNAME'),
        'password' => env('WEBDAV_PASSWORD'),
        'root' => env('WEBDAV_LOCAL_ROOT', 'cloud-drive/webdav'),
    ],
    's3' => [
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'root' => env('S3_LOCAL_ROOT', 'cloud-drive/s3'),
    ],
];
