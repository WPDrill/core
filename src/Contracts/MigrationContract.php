<?php

namespace CoreWP\Contracts;

use CoreWP\Plugin;

interface MigrationContract
{
    public function run(Plugin $plugin);
}
