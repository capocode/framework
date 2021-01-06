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
        $this->setupViews();

        $this->setupPaths();

        $this->setupCommands();

        $this->setupRoutes();
    }

    public function register()
    {
        $this->setupSiteCache();

        $this->setupPlugins();
    }

    private function setupRoutes()
    {
        if (File::exists(site_path('src/routes.php'))) {
            Route::middleware('web')
                // ->namespace($this->namespace)
                ->group(site_path('src/routes.php'));
        }

        Route::middleware('web')
            // ->namespace($this->namespace)
            ->group(__DIR__ . '/routes.php');
    }

    private function setupPaths()
    {
        $this->app->bind('path.public', function () {
            return site_path('public');
        });
    }

    private function setupViews()
    {
        $viewPaths = config('view.paths');

        config(['view.paths'  =>  [site_path('src')]]);
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
        File::ensureDirectoryExists(site_cache_path());
    }

}
