<?php

namespace WPDrill\Contracts;

use WPDrill\DB\Migration\Sql;
use WPDrill\Plugin;

interface MigrationContract
{
    public function up(): Sql;
    public function down(): Sql;
}
