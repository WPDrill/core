<?php

namespace WPDrill\Facades;

use WPDrill\DB\QueryBuilder\QueryBuilderHandler;
use WPDrill\Facade;

/**
 * @method static self table(string ...$tables)
 * @method static self from(string ...$tables)
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
 * @method static self transaction(\Closure $callback)
 */
class DB extends Facade
{
    public static function getFacadeAccessor()
    {
        return QueryBuilderHandler::class;
    }
}
