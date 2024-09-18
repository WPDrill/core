<?php

namespace WPDrill;

use WPDrill\Contracts\InvokableContract;
use WPDrill\Routing\RouteManager;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class Plugin
{
    protected static ?self $instance = null;
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
     * @var bool
     */
    protected bool $isDev;
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
    protected array $pluginConfig = [];

    public function __construct(string $file, string $containerClass = Container::class)
    {
        $this->file         = $file;
        $this->relativePath = plugin_dir_url($file);

        $path               = pathinfo($this->file);
        $this->path = $path['dirname'];

        $this->builder = new ContainerBuilder($containerClass);

        $this->pluginConfig = $configs = require_once $this->getPath('config/plugin.php');

        $this->version = $configs['version'] ?? '1.0.0';
        $this->name = $configs['name'] ?? 'WPDrill';
        $this->slug = $configs['slug'] ?? 'corewp';
        $this->isDev = $configs['is_dev'] ?? false;
        $this->restApiNamespace = $configs['rest_api_namespace'] ?? 'corewp';
        $this->providers = $configs['providers'] ?? [];
        static::$instance = $this;
    }

    public static function getInstance(?string $file = null): self
    {
        if (self::$instance === null) {
            if ($file === null) {
                $file = __FILE__;
            }

            self::$instance = new static($file);
        }

        return self::$instance;
    }

    public function make(?callable $fn = null): void
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

        if ($fn) {
            $fn($this->resolve(RouteManager::class));
        }

        if (php_sapi_name() === 'cli') {
            return;
        }

        $initHandlers = $this->pluginConfig['initial_handlers'] ?? [];

        $this->registerPluginHooks(
            $initHandlers['activated'] ?? null,
            $initHandlers['deactivated'] ?? null,
            $initHandlers['uninstalled'] ?? null
        );

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

    public function resolve(string $name)
    {
        return $this->container->get($name);
    }

    public function registerPluginHooks($activationHandler = null, $deactivationHandler = null, $uninstallHandler = null): void
    {
        if ($activationHandler) {
            register_activation_hook($this->file, $this->resolveHandler($activationHandler));
        }

        if ($deactivationHandler) {
            register_deactivation_hook($this->file, $this->resolveHandler($deactivationHandler));
        }

        if ($uninstallHandler) {
            register_uninstall_hook($this->file, $this->resolveHandler($uninstallHandler));
        }
    }

    public function resolveHandler(string $handler): InvokableContract
    {
        $handler = $this->resolve($handler);
        if (!$handler instanceof InvokableContract) {
            throw new \Exception('Handler must be an instance of InvokableContract');
        }

        return $handler;

    }

    public function isDevelopment(): bool {
        return $this->isDev;
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
