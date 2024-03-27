<?php

namespace WPDrill\Facades;

use WPDrill\Facade;
use WPDrill\Menus\Menu as MenuOption;

/**
 * @method static MenuOption add(string $pageTitle, $handler, string $capability)
 * @method static void remove(string $slug, ?string $submenuSlug = null)
 * @method static MenuOption group(string $pageTitle, $handler, string $capability, callable $fn)
 */
class Menu extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'menu';
    }
}
