<?php

use Illuminate\Support\Str;

function site_path(?string $path = null)
{
    $fullpath = getcwd();

    if (Str::startsWith($path, '/')) {
        $path = ltrim($path, '/');
    }

    if ($path) {
        $fullpath .= '/' . $path;
    }

    return $fullpath;
}
