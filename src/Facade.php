<?php

namespace CoreWP;

abstract class Facade
{
    protected static Plugin $app;
    protected static array $resolvedInstance = [];

    public static function setFacadeApplication(Plugin $app)
    {
        static::$app = $app;
    }

    public static function getFacadeApplication(): Plugin
    {
        return static::$app;
    }

    public static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        if (! $instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        return call_user_func_array([$instance, $method], $args);
    }

    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$app->resolve($name);
    }
}
