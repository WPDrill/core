<?php

namespace CoreWP\Facades;

use CoreWP\Facade;
use CoreWP\ViewManager;

/**
 * @method static string render(string $view, array $data = [])
 * @method static void output(string $view, array $data = [])
 * @method static ViewManager templating(bool $enable)
 */
class View extends Facade
{
    public static function getFacadeAccessor()
    {
        return ViewManager::class;
    }
}
