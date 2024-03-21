<?php

namespace CoreWP\Facades;

use CoreWP\Facade;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @method static array getAttributes()
 * @method static array getServerParams()
 * @method static array getCookieParams()
 * @method static ServerRequestInterface withCookieParams()
 * @method static array getQueryParams()
 * @method static ServerRequestInterface withQueryParams()
 */
class Request extends Facade
{
    public static function getFacadeAccessor()
    {
        return ServerRequestInterface::class;
    }
}
