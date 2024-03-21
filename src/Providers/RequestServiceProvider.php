<?php

namespace CoreWP\Providers;

use CoreWP\ConfigManager;
use CoreWP\Routing\RouteManager;
use CoreWP\ServiceProvider;
use Psr\Http\Message\ServerRequestInterface;

class RequestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->plugin->bind(ServerRequestInterface::class, function () {
            $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

            $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
                $psr17Factory, // ServerRequestFactory
                $psr17Factory, // UriFactory
                $psr17Factory, // UploadedFileFactory
                $psr17Factory  // StreamFactory
            );

            return $creator->fromGlobals();
        });
    }

    public function boot(): void
    {
    }
}
