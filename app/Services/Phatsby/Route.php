<?php

namespace App\Services\Phatsby;

use Illuminate\Support\Facades\File;

class Route
{
    public $url;

    public $data;

    public $staticContent;

    public function __construct(string $url, array $data, $staticContent = null)
    {
        $this->url = $url;

        $this->data = $data;

        $this->staticContent = $staticContent;
    }

    /**
     * Render the HTML for the route
     */
    public function render()
    {
        if ($this->staticContent) {
            return $this->staticContent;
        }
    }

    public function save()
    {
        $path = $this->url;

        $filename = $path  . '/index.html';

        if ($path === '/') {
            $filename = 'index.html';
        }

        $content = $this->render();

        $filepath = site_path(env('BUILD_DIR', 'dist') . '/' . $filename);

        File::ensureDirectoryExists(dirname($filepath));

        File::put($filepath, $content);
    }
}
