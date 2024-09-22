<?php

namespace WPDrill\Contracts;

use WPDrill\DB\Migration\Sql;
use WPDrill\Plugin;

interface ShortcodeContract
{
    public function render(array $attrs, string $content = null): string;
}
