<?php

namespace App\Services;

class WebDavStorage extends LocalStorage
{
    public function __construct(array $config)
    {
        $root = $config['root'] ?? 'cloud-drive/webdav';

        parent::__construct(array_merge($config, ['root' => $root]), 'webdav');
    }
}
