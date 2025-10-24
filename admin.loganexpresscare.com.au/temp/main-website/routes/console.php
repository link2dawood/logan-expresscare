<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

Artisan::command('app-version', function () {
    $this->comment(File::get(public_path() . '/version.txt'));
})->purpose('Display current version of app');
