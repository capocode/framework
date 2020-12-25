<?php

namespace Capo\Services\Phatsby;

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
        $this->registerProviders();
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

    private function registerProviders()
    {
        $providers = $this->getSiteServiceProviders();

        foreach ($providers as $provider) {
            App::register($provider);
        }
    }

    private function getSiteServiceProviders()
    {
        if (!file_exists(site_path('capo-config.php'))) {
            return [];
        }

        $config = include(site_path('capo-config.php'));

        return $config['providers'] ?? [];
    }
}
