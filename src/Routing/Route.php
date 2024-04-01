<?php

namespace WPDrill\Routing;

class Route
{
    protected $middleware = null;
    protected string $method;
    protected string $uri;
    protected string $name = '';
    protected $action;
    protected ?Group $group;

    public function __construct($method, $uri, $action, $group = null)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        if ($group) {
            $this->middleware = $group->getMiddleware();
        }
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
        if ($this->group) {
            return rtrim($this->group->getPrefix(), '/') . '/' . ltrim($this->uri, '/');
        }

        return $this->uri;
    }

    public function getOriginalUri(): string
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

    public function getName(): string
    {
        if ($this->group) {
            return $this->group->getName() . $this->name;
        }

        return $this->name;
    }

}
