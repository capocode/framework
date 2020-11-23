<?php

namespace App\Services\Phatsby;

use Illuminate\Support\Facades\File;

class Router
{

    /**
     * Get the routes for each file to be generated
     *
     * @return array
     */
    public function routes(): array
    {
        $routes = [];

        $themePages = File::allFiles(site_path('src/pages'));

        foreach ($themePages as $page) {
            $slug = str_replace('.blade.php', '', $page->getRelativePathname());

            $slug = $slug === 'index' ? '/' : $slug;

            $staticContent = $this->getStaticContent($slug);

            $route = new Route($slug, [], $staticContent);

            $routes[] = $route;
        }

        // dd($routes);

        return $routes;
    }

    private function getStaticContent($slug)
    {
        $staticContent = null;

        if ($slug === '/') {
            $slug = '/index';
        }

        $themePath = 'pages/' . $slug;

        try {
            $staticContent = view($themePath)->render();
        } catch (\Exception $e) {
            // dump($e->getMessage());
            return $staticContent;
        }

        return $staticContent;
    }
}
