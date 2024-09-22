<?php

namespace WPDrill\Providers;

use WPDrill\DB\QueryBuilder\QueryBuilderHandler;
use WPDrill\ServiceProvider;

class DBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(QueryBuilderHandler::class, function () {
            global $wpdb;

            $connection = new \WPDrill\DB\Connection($wpdb, ['prefix' => $wpdb->prefix]);

            return new QueryBuilderHandler($connection);
        });
    }

    public function boot(): void
    {
    }
}
