<?php

namespace WPDrill\Helpers\func;

use WPDrill\Response;

if (!function_exists('wpdrill_rest')) {
    function wpdrill_rest(array $data): \WPDrill\Response
    {
        return new Response($data);
    }
}

if (!function_exists('wpdrill_path')) {
    function wpdrill_path(array $segment): string
    {
        return implode(DIRECTORY_SEPARATOR, $segment);
    }
}
