<?php

use Illuminate\Support\Str;
use App\Services\Phatsby\Data;
use Illuminate\Support\Facades\File;

function site_path(?string $path = null)
{
    $fullpath = getcwd();

    if (Str::endsWith($fullpath, 'src')) {
        $fullpath = str_replace('/src', '', $fullpath);
    }

    if (Str::startsWith($path, '/')) {
        $path = ltrim($path, '/');
    }

    if ($path) {
        $fullpath .= '/' . $path;
    }

    return $fullpath;
}

function data(string $key, $data = null)
{
    $dataService = new Data();

    return $dataService->get($key);
}

function manifest(string $path): string
{
    $mixBuildDir = '/static/_assets';

    $manifest = json_decode(File::get(site_path($mixBuildDir . '/mix-manifest.json')), true);

    $manifestPath = $manifest[$path] ?? $manifest['/' . $path] ?? $path;

    return '/_assets' . $manifestPath;
}
