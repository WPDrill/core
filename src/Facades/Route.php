<?php

namespace WPDrill\Facades;

use WPDrill\Facade;
use WPDrill\Routing\RouteManager;
use Noodlehaus\ConfigInterface;

/**
 * @method static mixed get(string $uri, $action)
 * @method static mixed post(string $uri, $action)
 * @method static mixed put(string $uri, $action)
 * @method static mixed patch(string $uri, $action)
 * @method static mixed delete(string $uri, $action)
 * @method static void group(array $attributes, callable $callback)
 */
class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return RouteManager::class;
    }
}
