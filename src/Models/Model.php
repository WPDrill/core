<?php

namespace WPDrill\Models;

use WPDrill\DB\QueryBuilder\QueryBuilderHandler;
use WPDrill\Facade;


/**
 * @method static self select(string ...$columns)
 * @method static array|object|null get()
 * @method static \stdClass|null first()
 * @method static \stdClass|null findAll(string $fieldName, $value)
 * @method static \stdClass|null find($value, $fieldName = 'id')
 * @method static int count()
 * @method static array|string insert(array $data)
 * @method static array|string update(array $data)
 * @method static array|string updateOrInsert(array $data)
 * @method static mixed delete()
 * @method static self where(string $key, $operator = null, $value = null)
 * @method static self orWhere(string $key, $operator = null, $value = null)
 * @method static self whereIn(string $key, array $values)
 * @method static self whereNull(string $key)
 */
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
