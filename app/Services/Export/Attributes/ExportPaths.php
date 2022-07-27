<?php

namespace Capo\Services\Export\Attributes;

use Attribute;
use Capo\Services\Export\Interfaces\ExportPaths as ExportPathsInterface;

#[Attribute]
class ExportPaths
{
    public function __construct(private array|string $exportPaths)
    {
    }

    public function paths(): array
    {
        if (is_array($this->exportPaths)) {
            return $this->exportPaths;
        }

        $exception = new \Exception('ExportPaths must be an array or an instance of ' . ExportPathsInterface::class);

        try {
            $exportPaths = app($this->exportPaths);
        } catch (\Exception $e) {
            throw $exception;
        }

        if (!$exportPaths instanceof ExportPathsInterface) {
            throw $exception;
        }

        return $exportPaths->paths();
    }
}
