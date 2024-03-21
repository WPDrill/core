<?php

namespace CoreWP\Providers;

use CoreWP\ConfigManager;
use CoreWP\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ConfigManager::class, function () {
            return new \CoreWP\ConfigManager($this->plugin->getPath('config'));
        });
    }

    public function boot(): void
    {
    }
}
