<?php

namespace App\Services\Phatsby;

use App\Services\Phatsby\Console\Commands\PhatsbyBuild;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class PhatsbyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('path.public', function () {
            return site_path('public');
        });

        $this->commands([
            PhatsbyBuild::class,
        ]);

        // $this->loadViewsFrom($dir . '/src', 'site');

        Route::middleware('web')
            // ->namespace($this->namespace)
            ->group(__DIR__ . '/routes.php');

        if (File::exists(site_path('src/routes.php'))) {
            Route::middleware('web')
                // ->namespace($this->namespace)
                ->group(site_path('src/routes.php'));
        }
    }
}
