<?php

namespace Capo\Services\Capo;

use Capo\Attributes\ExportPaths;
use Capo\Http\Controllers\CatchAll;
use Capo\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Routing\Route as RoutingRoute;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class Router
{
    private Kernel $kernel;

    public function __construct()
    {
        $this->kernel = app(Kernel::class);
    }

    /**
     * Get the routes for each file to be generated
     *
     * @return array
     */
    public function routes(): array
    {
        $pageUris = $this->getRouteUrisFromPages();

        $registeredRoutes = collect(RouteFacade::getRoutes());

        $siteRegisteredRoutes = $this->removeNonAppRoutes($registeredRoutes);

        $exportUrls = $this->getExportUris($siteRegisteredRoutes);

        $allUris = array_merge($pageUris, $exportUrls);

        $routes = [];

        // in routes file
        foreach ($allUris as $url) {
            $routes[] = $this->makeRoute($url);
        }

        return $routes;
    }

    private function makeRoute(string $url)
    {
        $request = Request::create($url);
        $res = $this->kernel->handle($request);
        $html = $res->getContent();

        return new Route($url, [], $html);
    }

    /**
     * Get the routes from the pages
     * @param Collection<RoutingRoute> $routes
     * @return array<string>
     */
    private function getExportUris(Collection $routes): array
    {
        $uris = [];

        foreach ($routes as $route) {
            $routeAction = $route->getAction();

            if (is_callable($routeAction['uses'])) {
                $uris[] = $route->uri();
                continue;
            }

            $controller = $route->getController();
            $method = $route->getActionMethod();

            $reflector = new ReflectionMethod($controller, $method);

            /** @var ReflectionAttribute|null */
            $attribute = collect($reflector->getAttributes())
                ->first(function (ReflectionAttribute $attribute) {
                    return $attribute->getName() === ExportPaths::class;
                });

            if (!$attribute) {
                $uris[] = $route->uri();
                continue;
            }

            /** @var ExportPaths */
            $exportPathMapAttribute = $attribute->newInstance();
            $paths = $exportPathMapAttribute->paths();

            foreach ($paths as $path) {
                $uris[] = $path;
            }
        }

        return $uris;
    }

    /**
     * Filter out the fallback `{any}` and ignition routes
     * @return Collection<RoutingRoute>
     */
    private function removeNonAppRoutes(Collection $routes): Collection
    {
        return $routes->filter(function (RoutingRoute $r) {
            return !$this->isVendorRoute($r);
        });
    }

    private function getRouteUrisFromPages(): array
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

            $routes[] = $slug;
        }

        return $routes;
    }

    /**
     * Determine if the route has been defined outside of the application.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return bool
     */
    protected function isVendorRoute(RoutingRoute $route)
    {
        if ($route->action['uses'] instanceof Closure) {
            $path = (new ReflectionFunction($route->action['uses']))
                                ->getFileName();
        } elseif (
            is_string($route->action['uses']) &&
                  str_contains($route->action['uses'], 'SerializableClosure')
        ) {
            return false;
        } elseif (is_string($route->action['uses'])) {
            if ($this->isFrameworkController($route)) {
                return false;
            }

            $path = (new ReflectionClass($route->getControllerClass()))
                                ->getFileName();
        } else {
            return false;
        }

        if ($route->getControllerClass() === CatchAll::class) {
            return true;
        }

        return str_starts_with($path, site_path('vendor'));
    }

    /**
     * Determine if the route uses a framework controller.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return bool
     */
    protected function isFrameworkController(RoutingRoute $route)
    {
        return in_array($route->getControllerClass(), [
            '\Illuminate\Routing\RedirectController',
            '\Illuminate\Routing\ViewController',
        ], true);
    }
}
