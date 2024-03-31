<?php

namespace WPDrill\Models;

use WPDrill\DB\QueryBuilder\QueryBuilderHandler;
use WPDrill\Facade;

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
        return static::$table;
    }

    public static function getFacadeAccessor()
    {
        return QueryBuilderHandler::class;
    }

    /**
* @param $method
* @param $args
* @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());


        if (! $instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        $instance = $instance->table(self::getTableName());

        return call_user_func_array([$instance, $method], $args);
    }


}
