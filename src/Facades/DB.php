<?php

namespace CoreWP\Facades;

use CoreWP\DB\QueryBuilder\QueryBuilderHandler;
use CoreWP\Facade;

/**
 * @method static self table(string $table)
 * @method static self from(string ...$tables)
 * @method static self select(string ...$columns)
 */
class DB extends Facade
{
    public static function getFacadeAccessor()
    {
        return QueryBuilderHandler::class;
    }
}
