<?php

namespace WPDrill\Providers;

use WPDrill\ConfigManager;
use WPDrill\Facades\Config;
use WPDrill\ServiceProvider;

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

        $scripts = array_column(array_merge($adminScripts, $frontendScripts), null, 'handle');
        $styles = array_column(array_merge($adminStyles, $frontendStyles), null, 'handle');
        $this->addAttributeToScripts($scripts);
        $this->addAttributeToStyles($styles);
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

    protected function addAttributeToScripts(array $scripts)
    {
        add_filter('script_loader_tag', function( $tag, $handle) use($scripts) {

            $script = $scripts[$handle] ?? null;
            if ($script && str_contains($tag, '<script')) {
                $attrs = $script['attributes'] ?? [];
                foreach ($attrs as $key => $value) {
                    $tag = str_replace(' src', ' ' . $key . '="' . $value . '" src', $tag);
                }

                return $tag;
            }

            return $tag;
        }, 10, 2);
    }

    protected function addAttributeToStyles(array $styles)
    {
        add_filter('style_loader_tag', function( $tag, $handle) use($styles) {
            $style = $styles[$handle] ?? null;
            if ($style && str_contains($tag, '<link')) {
                $attrs = $style['attributes'] ?? [];
                foreach ($attrs as $key => $value) {
                    $tag = str_replace(' href', ' ' . $key . '="' . $value . '" href', $tag);
                }

                return $tag;
            }

            return $tag;
        }, 10, 2);
    }
}
