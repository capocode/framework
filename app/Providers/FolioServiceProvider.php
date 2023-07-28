<?php

namespace Capo\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Folio\Folio;

class FolioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $pagesDir = env('PAGES_DIR', 'pages');
        Folio::route(site_path($pagesDir), middleware: [
            '*' => [
                //
            ],
        ]);
    }
}
