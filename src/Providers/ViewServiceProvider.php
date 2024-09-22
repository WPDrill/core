<?php

namespace WPDrill\Providers;

use WPDrill\ServiceProvider;
use WPDrill\ViewManager;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ViewManager::class, function () {
            return new \WPDrill\ViewManager($this->plugin);
        });
    }

    public function boot(): void
    {
    }
}
