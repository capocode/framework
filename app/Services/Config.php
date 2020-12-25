<?php

namespace Capo\Services;

class Config
{
    public static function get()
    {
        if (!file_exists(site_path('capo-config.php'))) {
            return [];
        }

        return include(site_path('capo-config.php'));
    }

    public static function getPlugins()
    {
        $config = self::get();

        if (!isset($config['plugins'])) {
            return null;
        }

        return $config['plugins'];
    }

    public static function getPluginOptions($provider)
    {
        $plugins = self::getPlugins();

        $filteredPlugins = array_filter($plugins, function ($plugin) use ($provider) {
            return $plugin->serviceProvider === $provider;
        });

        if (count($filteredPlugins) > 0) {
            $plugin = $filteredPlugins[0];

            return $plugin->options;
        }

        return [];
    }
}
