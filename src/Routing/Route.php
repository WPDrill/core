<?php

namespace WPDrill\Routing;

class Route
{
    protected $middleware;
    protected string $method;
    protected string $uri;
    protected string $name = '';
    protected $action;
    protected string $prefix = '';
    protected ?Group $group;

    public function __construct($method, $uri, $action, $group = null)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->middleware = null;
        $this->group = $group;
    }

    public function middleware($middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getMiddleware()
    {
        if ($this->group) {
            return $this->group->getMiddleware();
        }

        return $this->middleware;
    }

    public function getPrefix(): string
    {
        if ($this->group) {
            return rtrim($this->group->getPrefix(), '/') . '/' . ltrim($this->prefix, '/');
        }

        return $this->prefix;
    }
    public function getName(): string
    {
        if ($this->group) {
            return $this->group->getName() . $this->name;
        }

        return $this->name;
    }

}
