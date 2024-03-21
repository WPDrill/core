<?php

namespace CoreWP\Facades;

use CoreWP\Facade;

/**
 * @method static void add(string $pageTitle, string $name, string $capability, string $slug, callable $handler = null, int $position = null, string $icon = '')
 * @method static void group(string $pageTitle, string $name, string $capability, string $slug, callable $handler = null, int $position = null, string $icon = '', callable $fn)
 */
class Menu extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'menu';
    }
}
