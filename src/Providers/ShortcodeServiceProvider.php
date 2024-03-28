<?php

namespace WPDrill\Providers;

use WPDrill\ServiceProvider;
use WPDrill\Shortcodes\ShortcodeManager;

class ShortcodeServiceProvider extends ServiceProvider
{
    protected ShortcodeManager $shortcode;

    public function register(): void
    {
        $this->plugin->bind('shortcode', function () {
            $this->shortcode = new ShortcodeManager($this->plugin);

            return $this->shortcode;
        });
    }

    public function boot(): void
    {
        add_action('init', function () {

            $shortcode = require $this->plugin->getPath('bootstrap/shortcodes.php');
            $shortcode($this->plugin);

            $this->shortcode->register();
        });
    }
}
