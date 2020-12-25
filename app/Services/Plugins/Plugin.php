<?php

namespace Capo\Services\Plugins;

use Illuminate\Support\ServiceProvider;

class Plugin
{
    public string $serviceProvider;

    public array $options;

    public function __construct(string $serviceProvider, array $options = [])
    {
        $this->serviceProvider = $serviceProvider;

        $this->options = $options;
    }
}
