<?php

namespace CoreWP\Providers;

use CoreWP\DB\QueryBuilder\QueryBuilderHandler;
use CoreWP\ServiceProvider;

class DBServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(QueryBuilderHandler::class, function () {
            global $wpdb;

            $connection = new \CoreWP\DB\Connection($wpdb, ['prefix' => $wpdb->prefix]);

            return new QueryBuilderHandler($connection);
        });
    }

    public function boot(): void
    {
    }
}
