<?php

namespace WPDrill\Helpers;

use WPDrill\Response;

if (!function_exists('wpdrill_rest')) {
    function wpdrill_rest(array $data): \WPDrill\Response
    {
        return new Response($data);
    }
}
