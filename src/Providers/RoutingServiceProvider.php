<?php

namespace CoreWP\Providers;

use CoreWP\ConfigManager;
use CoreWP\Routing\RouteManager;
use CoreWP\ServiceProvider;

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
