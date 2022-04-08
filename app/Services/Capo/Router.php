<?php

namespace Capo\Services\Capo;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\View\View;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Facades\Log;

class Router
{
    /**
     * Get the routes for each file to be generated
     *
     * @return array
     */
    public function routes(): array
    {
        $routes = $this->getRoutesFromPages();

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

    private function getHtml(View|InertiaResponse|Response $response)
    {
        if ($response instanceof InertiaResponse) {
            return $response->toResponse(request())->getContent();
        }

        if ($response instanceof Response) {
            return $response->getContent();
        }

        return $response->render();
    }

    private function getRouteResponse(RoutingRoute $route): View|InertiaResponse|Response
    {
        $routeAction = $route->getAction();

        if (is_callable($routeAction['uses'])) {
            return $routeAction['uses']();
        }

        $controller = $route->getController();
        $method = $route->getActionMethod();

        // Invoke the controller
        if ($controller::class === $method) {
            return app()->call($controller::class);
        }

        try {
            // Try invoking the controller method
            // If it's a dynamic route without data, it will throw an exception
            return app()->call([$controller, $method]);
        } catch (\Exception $e) {
            Log::error("Couldn't Render Route: {$route->uri()}");
            return response('', 404);
        }
    }

    private function getRoutesFromPages(): array
    {
        $routes = [];

        $pagesPath = app()->resourcePath('views/pages');

        if (!File::exists($pagesPath)) {
            return $routes;
        }

        $themePages = File::allFiles($pagesPath);

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
