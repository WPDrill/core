<?php

namespace WPDrill\Providers;

use WPDrill\DB\Migration\Migrator;
use WPDrill\Routing\RouteManager;
use WPDrill\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(Migrator::class, function () {
            return new Migrator($this->plugin->getPath('database/migrations'));
        });
    }

    public function boot(): void
    {
    }
}
