<?php

namespace Capo\Services\Phatsby;

use Error;
use Illuminate\Support\Facades\File;

class Data
{
    public function get(string $key)
    {
        $parts = explode('.', $key);

        $fileKey = array_pop($parts);

        $filePath = implode('/', $parts);

        $fullFilePath = site_path('src/data/' . $filePath);

        $phpFile = $fullFilePath . '.php';

        $jsonFile = $fullFilePath . '.json';

        $phpFileExists = File::exists($phpFile);

        $jsonFileExists = File::exists($jsonFile);

        if (!$phpFileExists && !$jsonFileExists) {
            throw new Error('Data file does not exist');
        }

        if ($phpFileExists && $jsonFileExists) {
            throw new Error('Only use one type of file for your config. PHP or JSON. Not both you silly goose.');
        }

        $arrayOrObject = null;

        if ($phpFileExists) {
            $arrayOrObject = include($phpFile);
        }

        if ($jsonFileExists) {
            $json = File::get($jsonFile);

            $arrayOrObject = json_decode($json);
        }

        $value = data_get($arrayOrObject, $fileKey);

        if (!$value) {
            throw new Error('Data value not found');
        }

        return $value;
    }
}
