<?php

namespace Capo\Services\Capo;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\View\View;
use Inertia\Response as InertiaResponse;

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

        array_merge($routes, $this->getRoutesFromPages());

        /** @var Collection<RoutingRoute> */
        $routeCollection = collect(RouteFacade::getRoutes());

        // Filter out the fallback `{any}` and ignition routes
        $routeCollection = $routeCollection->filter(function (RoutingRoute $r){
            return
                $r->uri() !== '{any}' &&
                $r->getPrefix() !== '_ignition';
        });

        // in routes file
        foreach ($routeCollection as $collectedRoute) {
            $routeResponse = $this->getRouteResponse($collectedRoute);

            $html = $this->getHtml($routeResponse);

            $routes[] = new Route($collectedRoute->uri(), [], $html);
        }

        return $routes;
    }

    private function getHtml(View|InertiaResponse $response)
    {
        if ($response instanceof InertiaResponse) {
            return $response->toResponse(request())->getContent();
        }

        return $response->render();
    }

    private function getRouteResponse(RoutingRoute $route): View|InertiaResponse
    {
        $routeAction = $route->getAction();

        if (is_callable($routeAction['uses'])) {
            return $routeAction['uses']();
        }

        $controller = $route->getController();
        $method = $route->getActionMethod();

        return $controller::class === $method
            ? app($method)() // invoke the controller
            : $controller->$method(); // invoke the method

    }

    private function getRoutesFromPages(): array
    {
        $routes = [];

        if (!File::exists(site_path('src/pages'))) {
            return $routes;
        }

        $themePages = File::allFiles(site_path('src/pages'));

        foreach ($themePages as $page) {
            $slug = str_replace('.blade.php', '', $page->getRelativePathname());

            $slug = $slug === 'index' ? '/' : $slug;

            $staticContent = $this->getStaticContent($slug);

            $route = new Route($slug, [], $staticContent);

            $routes[] = $route;
        }

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
            return $staticContent;
        }

        return $staticContent;
    }
}
