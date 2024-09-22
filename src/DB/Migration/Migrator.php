<?php

namespace WPDrill\DB\Migration;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WPDrill\Contracts\MigrationContract;
use WPDrill\Facades\Config;

class Migrator
{

    private string $migrationPath;
    protected array $migrationFiles = [];
    protected array $migrationNames = [];
    protected ?InputInterface $input = null;
    protected ?OutputInterface $output = null;
    private \wpdb $db;

    public function __construct(string $migrationPath, InputInterface $input = null, OutputInterface $output = null)
    {
        global $wpdb;

        $this->input = $input;
        $this->output = $output;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $this->migrationPath = $migrationPath;
        $this->db = $wpdb;
    }


    public function getMigrationInstances(array $files): array
    {
        $migrationInstances = [];

        foreach ($files as $key => $file) {
            $file = $this->getMigrationPath($file . '.php');
            if (!file_exists($file)) {
                continue;
            }

            $fileInfo = pathinfo($file);
            require_once $file;

            if (!class_exists($fileInfo['filename'])) {
                continue;
            }

            $instance = new $fileInfo['filename']();

            if (!$instance instanceof MigrationContract) {
                continue;
            }

            $migrationInstances[$fileInfo['filename']] = $instance;

        }

        return $migrationInstances;
    }

    protected function getAlreadyExecutedMigrations()
    {
        $query = "SELECT migration FROM {$this->getMigrationTableName()}";
        $result = $this->db->get_results($query);


        return array_column($result, 'migration');
    }

    protected function getNeedToMigrations(): array
    {
        $alreadyExecutedMigrations = $this->getAlreadyExecutedMigrations();
        $migrationFiles = $this->getMigrationNames();

        return array_diff($migrationFiles, $alreadyExecutedMigrations);
    }

    public function run()
    {
        if ($this->output) {
            $this->output->writeln('<info>Running migrations...</info>');
        }
        $this->createMigrationsTable();
        $migrations = $this->getNeedToMigrations();
        if (empty($migrations)) {
            if ($this->output) {
                $this->output->writeln('<comment>No migrations to run!</comment>');
            }
            return;
        }

        $migrationInstances = $this->getMigrationInstances($migrations);

        $lastBatch = $this->getLastBatch();

        foreach ($migrationInstances as $migration) {

            $this->up($migration);

            $this->db->insert($this->getMigrationTableName(), [
                'migration' => get_class($migration),
                'batch' => $lastBatch + 1
            ]);
        }

        if ($this->output) {
            $this->output->writeln('<info>Migration successfully finished!</info>');
        }

    }

    public function rollback()
    {
        if ($this->output) {
            $this->output->writeln('<info>Rolling back migrations...</info>');
        }

        $lastBatch = $this->getLastBatch();

        if ($lastBatch === 0) {
            if ($this->output) {
                $this->output->writeln('<comment>No migrations to rollback!</comment>');
            }

            return;
        }


        $query = "SELECT migration FROM {$this->getMigrationTableName()} WHERE batch = $lastBatch";
        $migrations = $this->db->get_results($query, ARRAY_A);

        $instances = $this->getRollbackInstances(array_column($migrations, 'migration'));

        foreach ($instances as $migration) {

            $this->down($migration);

            $this->db->delete($this->getMigrationTableName(), ['migration' => get_class($migration)]);
        }

        if ($this->output) {
            $this->output->writeln('<info>Rollback successfully finished!</info>');
        }

    }

    public function reset()
    {
        if ($this->output) {
            $this->output->writeln('<info>Resetting migrations...</info>');
        }

        $query = "SELECT migration FROM {$this->getMigrationTableName()} WHERE true";
        $migrations = $this->db->get_results($query, ARRAY_A);

        $instances = $this->getRollbackInstances(array_column($migrations, 'migration'));

        foreach ($instances as $migration) {

            $this->down($migration);
        }

        $query = "DROP TABLE IF EXISTS {$this->getMigrationTableName()}";

        $this->db->query($query);

        if ($this->output) {
            $this->output->writeln('<info>Reset successfully finished!</info>');
        }

    }

    protected function getRollbackInstances(array $migrations): array
    {
        $migrationInstances = [];

        foreach ($migrations as $key => $migration) {
            $migration = $this->getMigrationPath($migration . '.php');
            if (!file_exists($migration)) {
                continue;
            }

            $fileInfo = pathinfo($migration);
            require_once $migration;

            if (!class_exists($fileInfo['filename'])) {
                continue;
            }

            $instance = new $fileInfo['filename']();

            if (!$instance instanceof MigrationContract) {
                continue;
            }

            $migrationInstances[$fileInfo['filename']] = $instance;

        }

        return $migrationInstances;

    }

    protected function up( MigrationContract $migration)
    {
        if ($this->output) {
            $this->output->writeln('<comment>Migration: </comment> ' . get_class($migration));
        }

        $query = $migration->up();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
        if ($this->output) {
            $this->output->writeln('<info>Migrated: </info> ' . get_class($migration));
        }
    }

    protected function down( MigrationContract $migration)
    {
        if ($this->output) {
            $this->output->writeln('<comment>Rollback: </comment> ' . get_class($migration));
        }

        $query = $migration->down();
        $this->db->query($query);
        if ($this->output) {
            $this->output->writeln('<info>Rollbacked: </info> ' . get_class($migration));
        }
    }

    protected function scan(  )
    {
        $files = glob($this->getMigrationPath() . '/*.php');
        foreach ($files as $key => $file) {
            $fileInfo = pathinfo($file);
            if ($fileInfo['extension'] !== 'php') {
                continue;
            }

            $this->migrationFiles[] = $fileInfo['dirname'] . '/' . $fileInfo['filename'];
            $this->migrationNames[] = $fileInfo['filename'];
        }
    }

    public function isScanned(): bool
    {
        return count($this->migrationFiles) > 0;
    }

    protected function getMigrationFiles(): array
    {
        if (!$this->isScanned()) {
            $this->scan();
        }

        return $this->migrationFiles;
    }

    protected function getMigrationNames(): array
    {
        if (!$this->isScanned()) {
            $this->scan();
        }

        return $this->migrationNames;
    }

    protected function createMigrationsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS {$this->getMigrationTableName()} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255),
                    batch INT
                )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
    }

    protected function getLastBatch(): int
    {
        $query = "SELECT MAX(batch) as max_batch FROM {$this->getMigrationTableName()}";
        $result = $this->db->get_row($query, ARRAY_A);

        return $result['max_batch'] ?? 0;
    }

    protected function getMigrationPath(string $path = ''): string
    {
        if ($path === '') {
            return $this->migrationPath;
        }

        return $this->migrationPath . '/' . ltrim($path, '/');
    }

    protected function getMigrationTableName(): string
    {
        return $this->db->prefix . (rtrim(str_replace('-', '_', strtolower(Config::get('plugin.prefix'))), '_') . '_') . 'migrations';

    }

}
