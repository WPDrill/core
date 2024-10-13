<?php

namespace WPDrill\Providers;

use WPDrill\ServiceProvider;
use WPDrill\Views\ViewManager;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ViewManager::class, function () {
            return new \WPDrill\Views\ViewManager($this->plugin);
        });
    }

    public function boot(): void
    {

    }
}
