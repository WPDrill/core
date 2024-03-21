<?php

namespace CoreWP\Providers;

use CoreWP\ConfigManager;
use CoreWP\Facades\Config;
use CoreWP\ServiceProvider;

class EnqueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $adminScripts = Config::get('enqueue.admin.scripts', []);
        $adminStyles = Config::get('enqueue.admin.styles', []);
        $frontendScripts = Config::get('enqueue.frontend.scripts', []);
        $frontendStyles = Config::get('enqueue.frontend.styles', []);

        $this->registerAdminEnqueue($adminScripts, $adminStyles);
        $this->registerFrontendEnqueue($frontendScripts, $frontendStyles);
    }

    protected function registerAdminEnqueue(array $scripts, array $styles): void
    {
        add_action('admin_enqueue_scripts', function () use ($scripts, $styles) {
            foreach ($scripts as $script) {
                wp_enqueue_script($script['handle'], $this->plugin->getRelativePath($script['src']), $script['deps'], $script['ver'], $script['in_footer']);
            }

            foreach ($styles as $style) {
                wp_enqueue_style($style['handle'], $this->plugin->getRelativePath($style['src']), $style['deps'], $style['ver'], $style['media']);
            }
        });
    }

    protected function registerFrontendEnqueue(array $scripts, array $styles): void
    {
        add_action('wp_enqueue_scripts', function () use ($scripts, $styles) {
            foreach ($scripts as $script) {
                wp_enqueue_script($script['handle'], $this->plugin->getRelativePath($script['src']), $script['deps'], $script['ver'], $script['in_footer']);
            }

            foreach ($styles as $style) {
                wp_enqueue_style($style['handle'], $this->plugin->getRelativePath($style['src']), $style['deps'], $style['ver'], $style['media']);
            }
        });
    }
}
