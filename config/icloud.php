<?php

return [
    'default' => env('ICLOUD_STORAGE', 's3'),
    'webdav' => [
        'base_uri' => env('WEBDAV_BASE_URI'),
        'username' => env('WEBDAV_USERNAME'),
        'password' => env('WEBDAV_PASSWORD'),
    ],
    's3' => [
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
];
