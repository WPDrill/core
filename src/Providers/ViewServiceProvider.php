<?php

namespace CoreWP\Providers;

use CoreWP\ServiceProvider;
use CoreWP\ViewManager;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ViewManager::class, function () {
            return new \CoreWP\ViewManager($this->plugin);
        });
    }

    public function boot(): void
    {
    }
}
