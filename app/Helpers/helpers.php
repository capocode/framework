<?php

function site_path(?string $path = null)
{
    $fullpath = getcwd();

    if ($path) {
        $fullpath .= '/' . $path;
    }

    return $fullpath;
}
