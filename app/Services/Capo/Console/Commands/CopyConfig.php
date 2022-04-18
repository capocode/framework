<?php

namespace Capo\Services\Capo\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CopyConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:eject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy the default config folder to the site folder';

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
        $this->info('Ejecting config directory...');

        File::copyDirectory(base_path('config'), site_path('config'));

        $this->info('Done!');
        return 0;
    }
}
