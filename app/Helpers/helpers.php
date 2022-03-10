<?php

use Illuminate\Support\Str;
use Capo\Services\Capo\Data;
use Illuminate\Support\Facades\File;

function site_path(?string $path = null)
{
    $fullpath = getcwd();

    if (Str::endsWith($fullpath, 'public')) {
        $fullpath = str_replace('/public', '', $fullpath);
    }

    if (Str::startsWith($path, '/')) {
        $path = ltrim($path, '/');
    }

    if ($path) {
        $fullpath .= '/' . $path;
    }

    return $fullpath;
}

function site_cache_path()
{
    return site_path(env('CACHE_DIR', '.cache'));
}

function data(string $key, $data = null)
{
    $dataService = new Data();

    return $dataService->get($key);
}
