<?php

namespace Capo\Foundation;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Illuminate\Foundation\PackageManifest;

class Application extends \Illuminate\Foundation\Application
{
    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath, $sitePath)
    {
        // Set the site path before the base path because
        // the base path binds the paths in the container.
        $this->setSitePath($sitePath);
        $this->setBasePath($basePath);

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
        $this->singleton(Mix::class);

        $this->singleton(PackageManifest::class, function () {
            return new PackageManifest(
                new Filesystem,
                $this->sitePath(),
                $this->getCachedPackagesPath()
            );
        });
    }

    public function setSitePath($sitePath)
    {
        $this->sitePath = rtrim($sitePath, '\/');

        return $this;
    }

    /**
     * Get the base path of the Capo installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function sitePath($path = '')
    {
        return $this->sitePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->sitePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param  string  $path
     * @return string
     */
    // public function configPath($path = '')
    // {
    //     if (file_exists($this->sitePath('config'))) {
    //         return $this->sitePath.DIRECTORY_SEPARATOR.'config'.($path != '' ? DIRECTORY_SEPARATOR.$path : '');
    //     }

    //     return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path != '' ? DIRECTORY_SEPARATOR.$path : '');
    // }

    /**
     * Get the application namespace.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if (!is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents($this->sitePath('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath($this->path()) === realpath($this->sitePath($pathChoice))) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }
}
