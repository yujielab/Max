<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('"Simplicity is the soul of efficiency." — Austin Freeman');
})->purpose('Display an inspiring quote');
