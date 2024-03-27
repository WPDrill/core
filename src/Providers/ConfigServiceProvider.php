<?php

namespace WPDrill\Providers;

use WPDrill\ConfigManager;
use WPDrill\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ConfigManager::class, function () {
            return new \WPDrill\ConfigManager($this->plugin->getPath('config'));
        });
    }

    public function boot(): void
    {
    }
}
