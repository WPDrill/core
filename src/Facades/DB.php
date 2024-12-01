<?php

namespace WPDrill\Facades;

use WPDrill\DB\QueryBuilder\QueryBuilderHandler;
use WPDrill\Facade;

/**
 * @method static QueryBuilderHandler table(string ...$tables)
 * @method static QueryBuilderHandler from(string ...$tables)
 * @method static QueryBuilderHandler select(string ...$columns)
 * @method static array|object|null get()
 * @method static \stdClass|null first()
 * @method static \stdClass|null findAll(string $fieldName, $value)
 * @method static \stdClass|null find($value, $fieldName = 'id')
 * @method static int count()
 * @method static array|string insert(array $data)
 * @method static array|string update(array $data)
 * @method static array|string updateOrInsert(array $data)
 * @method static mixed delete()
 * @method static QueryBuilderHandler where(string $key, $operator = null, $value = null)
 * @method static QueryBuilderHandler orWhere(string $key, $operator = null, $value = null)
 * @method static QueryBuilderHandler whereIn(string $key, array $values)
 * @method static QueryBuilderHandler whereNull(string $key)
 * @method static QueryBuilderHandler transaction(\Closure $callback)
 * @method static void chunk(int $count, callable $callback);
 */
class DB extends Facade
{
    public static function getFacadeAccessor()
    {
        return QueryBuilderHandler::class;
    }
}
