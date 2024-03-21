<?php

namespace CoreWP;

use CoreWP\Contracts\InvokableContract;
use CoreWP\Routing\RouteManager;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Plugin
{
    protected string $file;
    protected static self $app;
    protected ContainerBuilder $builder;
    protected Container $container;
    protected array $events = [];
    //protected string $file;
    protected string $path;
    protected string $relativePath;
    /**
     * @var mixed|string
     */
    protected string $version;
    /**
     * @var mixed|string
     */
    protected string $name;
    /**
     * @var mixed|string
     */
    protected string $slug;
    /**
     * @var mixed|string
     */
    protected string $restApiNamespace;
    protected array $providers = [];

    public function __construct(string $file, string $containerClass = Container::class)
    {
        $this->file         = $file;
        $this->relativePath = plugin_dir_url($file);

        $path               = pathinfo($this->file);
        $this->path = $path['dirname'];

        $this->builder = new ContainerBuilder($containerClass);

        $configs = require_once $this->getPath('config/plugin.php');

        $this->version = $configs['version'] ?? '1.0.0';
        $this->name = $configs['name'] ?? 'CoreWP';
        $this->slug = $configs['slug'] ?? 'corewp';
        $this->restApiNamespace = $configs['rest_api_namespace'] ?? 'corewp';
        $this->providers = $configs['providers'] ?? [];
    }

    public function make(callable $fn): void
    {
        $providerInstance = [];
        foreach ($this->providers as $provider) {
            $provider = new $provider($this);
            $providerInstance[] = $provider;
            $provider->register();
        }
        $this->container = $this->builder->build();

        foreach ($this->events as $name => $handler) {
            $this->eventFire($name);
        }

        foreach ($providerInstance as $provider) {
            $provider->boot();
        }

        $fn($this->resolve(RouteManager::class));
    }

    public function bind(string $name, callable $resolver): void
    {
        $this->builder->addDefinitions([
            $name => $resolver
        ]);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function resolve(string $name): mixed
    {
        return $this->container->get($name);
    }

    public function registerPluginHooks(?InvokableContract $activationHandler = null, ?InvokableContract $deactivationHandler = null, ?InvokableContract $uninstallHandler = null): void
    {
        if ($activationHandler) {
            register_activation_hook($this->file, $activationHandler);
        }
        if ($deactivationHandler) {
            register_deactivation_hook($this->file, $deactivationHandler);
        }
        if ($uninstallHandler) {
            register_uninstall_hook($this->file, $uninstallHandler);
        }
    }
    public function registerActivationHook(string $file, InvokableContract $handler): void
    {
        register_activation_hook($file, $handler);
    }

    public function eventRegister(string $name, InvokableContract $handler): void
    {
        $this->events[$name] = $handler;
    }

    public function eventFire(string $name): void
    {
        $handler = $this->events[$name] ?? null;

        if ($handler) {
            add_action($name, $handler);
        }
    }

    public function getPath(string $path = ''): string
    {
        return rtrim($this->path, '/') . '/' . ltrim($path, '/');
    }

    public function getRelativePath(string $path = ''): string
    {
        return rtrim($this->relativePath, '/') . '/' . ltrim($path, '/');
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getRestApiNamespace(): string
    {
        return $this->restApiNamespace;
    }

    public function versionCompare(string $version, string $operator): bool
    {
        return version_compare($this->getVersion(), $version, $operator);
    }
}
