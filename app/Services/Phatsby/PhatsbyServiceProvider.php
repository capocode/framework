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
        $viewPaths = config('view.paths');

        config(['view.paths'  =>  [site_path('src')]]);


        $this->app->bind('path.public', function () {
            return site_path('static');
        });

        $this->commands([
            PhatsbyBuild::class,
        ]);

        if (File::exists(site_path('src/routes.php'))) {
            Route::middleware('web')
                // ->namespace($this->namespace)
                ->group(site_path('src/routes.php'));
        }

        Route::middleware('web')
            // ->namespace($this->namespace)
            ->group(__DIR__ . '/routes.php');
    }
}
