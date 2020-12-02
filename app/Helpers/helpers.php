<?php

use Illuminate\Support\Str;

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
