<?php

namespace WPDrill\Routing;

class Group
{
    protected string $prefix = '';
    protected string $name = '';
    protected $middleware = null;

    public function __construct($prefix, $name, $middleware)
    {
        $this->prefix = $prefix;
        $this->name = $name;
        $this->middleware = $middleware;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getMiddleware()
    {
        return $this->middleware;
    }

    public function merge(Group $group)
    {
        $this->prefix = rtrim($group->getPrefix(), '/') . '/' . ltrim($this->prefix, '/');
        $this->name = $group->getName() . $this->name;
        $this->middleware = $group->getMiddleware();

        return $this;

    }
}
