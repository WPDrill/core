<?php

namespace WPDrill\DB\Migration;

use WPDrill\Contracts\MigrationContract;
use WPDrill\Facades\Config;

abstract class Migration implements MigrationContract
{
    public function __construct()
    {

    }

    protected function table(string $name): string
    {
        global $wpdb;

        return $wpdb->prefix . (rtrim(str_replace('-', '_', strtolower(Config::get('plugin.prefix'))), '_') . '_') . $name;
    }

    public function createTable(string $name, array $columns): Sql
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->table($name)} (";
        foreach ($columns as $key => $value) {
            end($columns);
            if ($key === key($columns)) {
                $query .= "{$key} {$value}";
                continue;
            }

            $query .= "{$key} {$value}, ";
        }

        $query .= ")";

        return new Sql($query);
    }

    public function dropTable(string $name): Sql
    {
        $query = "DROP TABLE IF EXISTS {$this->table($name)}";

        return new Sql($query);
    }

    public function addColumns(string $table, array $columns): Sql
    {
        $query = "ALTER TABLE {$this->table($table)} ADD ";
        $query .= implode(', ADD ', $columns);

        return new Sql($query);
    }

    public function dropColumns(string $table, array $columns): Sql
    {
        $query = "ALTER TABLE {$this->table($table)} DROP COLUMN ";
        $query .= implode(', DROP COLUMN ', $columns);

        return new Sql($query);
    }

    public function renameColumns(string $table, array $columns): Sql
    {
        $query = "ALTER TABLE {$this->table($table)} CHANGE ";
        $query .= implode(', CHANGE ', $columns);

        return new Sql($query);
    }

    public function updateTable(string $table, array $columns): Sql
    {
        $query = "ALTER TABLE {$this->table($table)} ";
        $query .= implode(', ', $columns);

        return new Sql($query);
    }

    public function renameTable(string $oldName, string $newName): Sql
    {
        $query = "RENAME TABLE {$this->table($oldName)} TO {$this->table($newName)}";

        return new Sql($query);
    }

}
