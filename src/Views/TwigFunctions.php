<?php

namespace WPDrill\Views;

use Twig\Extension\AbstractExtension;
use WPDrill\Facades\Config;

class TwigFunctions extends AbstractExtension
{
    public function getFunctions()
    {
        $functions = Config::get('view.functions', []);

        $twigFuncs = [];

        foreach ($functions as $name => $function) {
            $twigFuncs[] = new \Twig\TwigFunction($name, $function);
        }

        return $twigFuncs;
    }
}
