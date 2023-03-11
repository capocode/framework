<?php

namespace Capo\Services\Capo;

use Capo\Services\Config;
use Capo\Services\Capo\Console\Commands\CapoBuild;
use Capo\Services\Capo\Console\Commands\CopyConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class CapoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->setupCommands();
    }

    public function register()
    {
        $this->setupSiteCache();

        $this->setupSiteConfig();

        $this->setupPlugins();
    }

    private function setupCommands()
    {
        $this->commands([
            CapoBuild::class,
            CopyConfig::class,
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

    private function setupSiteConfig()
    {
        $siteConfigPath = site_path('config');

        if (!File::exists($siteConfigPath)) {
            return;
        }

        foreach (Finder::create()->in($siteConfigPath)->name('*.php') as $file) {
            $this->mergeConfigFrom($file->getRealPath(), basename($file->getRealPath(), '.php'));
        }
    }

    private function setupSiteCache()
    {
        // Use storage dir instead?
        // File::ensureDirectoryExists(site_cache_path());
    }

}
