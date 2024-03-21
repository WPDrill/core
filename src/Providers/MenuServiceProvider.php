<?php

namespace CoreWP\Providers;

use CoreWP\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind('menu', function () {
            return new \CoreWP\MenuBuilder($this->plugin);
        });
    }

    public function boot(): void
    {
        add_action('admin_menu', function () {
            $menu = require $this->plugin->getPath('bootstrap/menu.php');
            $menu($this->plugin);
        });
    }
}
