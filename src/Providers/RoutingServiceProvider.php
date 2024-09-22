<?php

namespace WPDrill\Providers;

use WPDrill\ConfigManager;
use WPDrill\Routing\RouteManager;
use WPDrill\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(RouteManager::class, function () {
            $config = $this->plugin->resolve(ConfigManager::class);
            return new RouteManager($config, $this->plugin);
        });
    }

    public function boot(): void
    {
    }
}
