<?php 
namespace WPDrill;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
class LocalizationForTwig extends AbstractExtension{
    public function getFunctions()
    {
        return [
            new TwigFunction('__', function ($text, $domain = 'default') {
                return __($text, $domain);
            }),
        ];
    }
}