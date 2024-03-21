<?php

namespace CoreWP;

use CoreWP\Contracts\InvokableContract;

class MenuBuilder
{
    protected Plugin $plugin;
    protected string $group = '';

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function add(string $pageTitle, string $name, string $capability, string $slug, callable|InvokableContract $handler = null, int $position = null, string $icon = ''): void
    {
        if ($this->group !== '') {
            $parentSlug = $this->group;
            add_submenu_page(
                $parentSlug,
                $pageTitle,
                $name,
                $capability,
                $slug,
                $handler,
                $position
            );

            return;
        }

        add_menu_page(
            $pageTitle,
            $name,
            $capability,
            $slug,
            $handler,
            $icon,
            $position
        );
    }

    public function group(string $pageTitle, string $name, string $capability, string $slug, callable|InvokableContract $handler = null, int $position = null, string $icon = '', callable $fn): void
    {
        $this->group = '';

        add_menu_page(
            $pageTitle,
            $name,
            $capability,
            $slug,
            $handler,
            $icon,
            $position
        );

        $this->group = $slug;

        $fn($this);

        $this->group = '';
        remove_submenu_page($slug, $slug);
    }
}
