<?php

namespace WPDrill\Facades;

use WPDrill\Facade;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @method static array getServerParams()
 * @method static array getAttributes()
 * @method static mixed getAttribute(string $name, $default = null);
 * @method static ServerRequestInterface withAttribute(string $name, $value)
 * @method static ServerRequestInterface withoutAttribute(string $name)
 * @method static array getCookieParams()
 * @method static ServerRequestInterface withCookieParams(array $cookies)
 * @method static array getQueryParams()
 * @method static ServerRequestInterface withQueryParams(array $query)
 * @method static array getUploadedFiles()
 * @method static ServerRequestInterface withUploadedFiles(array $uploadedFiles)
 * @method static mixed getParsedBody()
 * @method static ServerRequestInterface withParsedBody(mixed $data)
 */
class Request extends Facade
{
    public static function getFacadeAccessor()
    {
        return ServerRequestInterface::class;
    }
}
