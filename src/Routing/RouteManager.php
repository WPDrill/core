<?php

namespace WPDrill\Routing;

use WPDrill\Plugin;
use WPDrill\ConfigManager;
use WPDrill\Contracts\InvokableContract;

class RouteManager
{
    protected array $routes = [];
    protected string $slug = '';
    protected string $prefix = '';
    protected ConfigManager $config;
    protected Plugin $app;
    protected Route $route;
    protected array $groupStack = [];


    public function __construct(ConfigManager $config, Plugin $plugin)
    {
        $this->app = $plugin;
        $this->config = $config;
        $this->slug = rtrim($this->config->get('plugin.slug'), '/');
    }

    public function addRoute($method, $uri, $action)
    {
        $group = null;
        if ($this->hasGroupStack()) {
            $group = $this->lastGroupStack();
        }
        $this->route = new Route($method, $uri, $action, $group);
        $this->routes[] = $this->route;

        return $this->route;
    }

    public function group(array $attributes, callable $callback)
    {
        $group = (new Group($attributes['prefix'] ?? '', $attributes['name'] ?? '', $attributes['middleware'] ?? ''));
        if ($this->hasGroupStack()) {
            $lastGroup = $this->lastGroupStack();
            $group = $group->merge($lastGroup);
        }

        $this->groupStack[] = $group;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function hasGroupStack()
    {
        return count($this->groupStack) > 0;
    }

    public function getGroupStack()
    {
        return $this->groupStack;
    }

    public function lastGroupStack(): Group
    {
        return end($this->groupStack);
    }

    public function get(string $uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }


    public function loadRoutes()
    {
        require_once $this->app->getPath('routes/api.php');

//        $this->getRoutes();
        $this->dispatch();
    }

    public function dispatch()
    {
        add_action('rest_api_init', function () {
            /**
             * @var Route $route
             */
            foreach ($this->routes as $route) {
                $instance = null;


                if (is_array($route->getAction()) && count($route->getAction()) == 2) {
                    $class = $this->app->resolve($route->getAction()[0]);
                    $instance = [$class, $route->getAction()[1]];
                }

                if (is_string($route->getAction()) && class_exists($route->getAction())) {
                    $instance = $this->app->resolve($route->getAction());
                    if (!$instance instanceof InvokableContract) {
                        throw new \Exception('Route action must be an instance of InvokableContract or callable or array with 2 elements.');
                    }
                }

                if (is_callable($route->getAction())) {
                    $instance = $route->getAction();
                }

                if ($route->getAction() instanceof InvokableContract) {
                    $instance = $route->getAction();
                }

                register_rest_route($this->slug, $this->makeUri($route), [
                    'methods' => $route->getMethod(),
                    'callback' => $instance,
                    'permission_callback' => $route->getMiddleware() ?? function () {
                            return true;
                        },
                ]);
            }
        });
    }


    public function makeUri(Route $route): string
    {
        $prefix = $route->getPrefix();
        if ($prefix !== '') {
            return rtrim($prefix, '/') . '/' . ltrim($route->getUri(), '/');
        }
        return '/' . ltrim($route->getUri(), '/');
    }
}
