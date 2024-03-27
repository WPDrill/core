<?php

namespace WPDrill\Providers;

use WPDrill\Menus\MenuBuilder;
use WPDrill\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    protected MenuBuilder $builder;

    public function register(): void
    {
        $this->plugin->bind('menu', function () {
            $this->builder = new \WPDrill\Menus\MenuBuilder($this->plugin);

            return $this->builder;
        });
    }

    public function boot(): void
    {
        add_action('admin_menu', function () {
            $menu = require $this->plugin->getPath('bootstrap/menu.php');
            $menu($this->plugin);

            $this->builder->register();
        });
    }
}
