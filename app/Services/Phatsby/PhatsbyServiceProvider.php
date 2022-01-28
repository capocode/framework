<?php

namespace Capo\Services\Phatsby;

use Capo\Services\Config;
use Capo\Services\Phatsby\Console\Commands\PhatsbyBuild;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class PhatsbyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setupCommands();
    }

    public function register()
    {
        $this->setupSiteCache();

        $this->setupPlugins();
    }

    private function setupCommands()
    {
        $this->commands([
            PhatsbyBuild::class,
        ]);
    }

    private function setupPlugins()
    {
        $plugins = Config::getPlugins();

        if (!$plugins) {
            return;
        }

        foreach ($plugins as $plugin) {
            App::register($plugin->serviceProvider);
        }
    }

    private function setupSiteCache()
    {
        // Use storage dir instead?
        // File::ensureDirectoryExists(site_cache_path());
    }

}
