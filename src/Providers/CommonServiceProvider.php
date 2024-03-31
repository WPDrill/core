<?php

namespace WPDrill\Providers;

use WPDrill\Facades\Config;
use WPDrill\ServiceProvider;

class CommonServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $postTypes = Config::get('post-types', []);
        $this->registerPostTypes($postTypes);
    }

    protected function registerPostTypes(array $postTypes): void
    {
        foreach ($postTypes as $type => $config) {
            $cpt = function() use ($type, $config) {
                register_post_type($type, $config);
            };

            add_action('init', $cpt);
        }
    }
}
