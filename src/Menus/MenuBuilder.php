<?php

namespace WPDrill\Menus;

use WPDrill\Contracts\InvokableContract;
use WPDrill\Plugin;

class MenuBuilder
{
    protected Plugin $plugin;
    protected ?Menu $group = null;

    protected array $menus = [];
    protected Menu $menu;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function add(string $pageTitle, $handler, $capability = 'read'): Menu
    {
        if (is_string($handler) && class_exists($handler)) {
            $handler = $this->plugin->resolve($handler);
            if (!$handler instanceof InvokableContract) {
                throw new \Exception('Handler must be an instance of InvokableContract');
            }
        }

        if(is_array($handler) && count($handler) == 2 && is_string($handler[0]) && class_exists($handler[0])) {
            $instance = $this->plugin->resolve($handler[0]);

            $handler = [$instance, $handler[1]];
        }

        $this->menu = new Menu($pageTitle, $handler, $capability);
        $this->menus[] = $this->menu;

        if ($this->group) {
            $this->menu->parentSlug($this->group->getSlug());
            $this->menu->slug($this->group->getSlug() . '_' . $this->menu->getSlug());
        }

        return $this->menu;
    }

    public function remove(string $slug, string $submenuSlug = null)
    {
        if ($submenuSlug) {
            remove_submenu_page($slug, $submenuSlug);
        } else {
            remove_menu_page($slug);
        }
    }

    public function group(string $pageTitle, $handler, $capability, callable $fn)
    {
        $this->group = $this->add($pageTitle, $handler, $capability);
        $fn($this);
        $this->group = null;
    }

    public function register(  ) {
        /**
            * @var Menu $menu
         */
        foreach ($this->menus as $menu) {
            if ($menu->hasParent()) {
                add_submenu_page(
                    $menu->getParentSlug(),
                    $menu->getPageTitle(),
                    $menu->getName(),
                    $menu->getCapability(),
                    $menu->getSlug(),
                    $menu->getHandler(),
                    $menu->getPosition()
                );
            } else {
                add_menu_page(
                    $menu->getPageTitle(),
                    $menu->getName(),
                    $menu->getCapability(),
                    $menu->getSlug(),
                    $menu->getHandler(),
                    $menu->getIcon(),
                    $menu->getPosition()
                );
            }
        }
    }

    public function currentGroup(): ?Menu
    {
        return $this->group;
    }
}
