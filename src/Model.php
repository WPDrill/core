<?php

namespace CoreWP;

use CoreWP\DB\QueryBuilder\QueryBuilderHandler;

abstract class Model extends Facade
{
    /**
     * @var string
     */
    protected static $table;

    /**
     * @var string
     */
    protected $primaryKey = 'id';


    public static function getTableName(): string
    {
        return '';
    }

    public static function getFacadeAccessor()
    {
        return QueryBuilderHandler::class;
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());


        if (! $instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        $instance = $instance->table(static::getTableName());

        return call_user_func_array([$instance, $method], $args);
    }


}
