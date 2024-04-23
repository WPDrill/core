<?php

namespace WPDrill\Menus;

class Menu
{
    protected string $pageTitle = '';
    protected string $name = '';
    protected string $capability = '';
    protected string $slug = '';
    protected $handler = null;
    protected ?int $position = null;
    protected string $icon = '';
    protected string $parentSlug = '';

    public function __construct(string $pageTitle, $handler, $capability) {
        $this->pageTitle = $pageTitle;
        $this->name = $pageTitle;
        $this->capability = $capability;
        $this->slug = $this->toUrlParam($pageTitle);
        $this->handler = $handler;
    }

    public function name(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function capability(string $capability): self {
        $this->capability = $capability;

        return $this;
    }

    public function slug(string $slug): self {
        $this->slug = $slug;

        return $this;
    }

    public function position(int $position): self {
        $this->position = $position;

        return $this;
    }

    public function icon(string $icon): self {
        $this->icon = $icon;

        return $this;
    }

    public function parentSlug(string $slug): self {
        $this->parentSlug = $slug;

        return $this;
    }
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getCapability(): string {
        return $this->capability;
    }

    public function getSlug(): string {
        return $this->slug;
    }

    public function getHandler() {
        return $this->handler;
    }

    public function getPosition(): ?int {
        return $this->position;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function getParentSlug(): string {
        return $this->parentSlug;
    }

    public function hasParent(): bool {
        return !empty($this->parentSlug);
    }

    protected function toSnakeCase($string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    protected function toUrlParam(string $string): string
    {
        return strtolower(str_replace([' ', '-'], '_', $string));
    }
}
