<?php

namespace CoreWP\Facades;

use CoreWP\Facade;
use CoreWP\Routing\RouteManager;
use Noodlehaus\ConfigInterface;

/**
 * @method static mixed get(string $uri, callable $action)
 * @method static mixed post(string $uri, callable $action)
 */
class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return RouteManager::class;
    }
}
