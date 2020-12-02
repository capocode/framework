<?php

namespace App\Services\Phatsby\Console\Commands;

use App\Services\Phatsby\Router;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PhatsbyBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the website to static site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Building site...');

        $router = new Router();

        $routes = $router->routes();

        foreach ($routes as $route) {
            $route->save();
        }

        $staticDirectory = site_path('static');

        // Copy static folder to build output
        if (File::exists($staticDirectory)) {
            File::copyDirectory($staticDirectory, site_path('public'));
        }

        system('npm run prod');

        return 0;
    }
}