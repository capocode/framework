<?php

namespace Capo\Attributes;

use Attribute;

#[Attribute]
class ExportPaths
{
    public function __construct(private string $exportPathsClass)
    {
    }

    public function paths(): array
    {
        return app($this->exportPathsClass)->paths();
    }
}
