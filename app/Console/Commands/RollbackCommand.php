<?php

namespace App\Console\Commands;

use App\Core\Command;
use App\Core\Config;
use App\Core\Database;
use Illuminate\Support\Carbon;
use RuntimeException;

class RollbackCommand extends Command
{
    protected string $name = 'migrate:rollback';
    protected string $description = 'Rollback the last batch of migrations';

    private Config $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    public function handle(array $arguments): int
    {
        try {
            $this->initDatabase();
            $this->info("Using database driver: " . Database::getDriverName());

            if (!$this->migrationsTableExists()) {
                $this->error("Migrations table does not exist");
                return 1;
            }

            $batch = $this->getLastBatchNumber();
            $migrations = $this->getMigrationsForBatch($batch);

            if (empty($migrations)) {
                $this->info("Nothing to rollback");
                return 0;
            }

            $count = 0;
            foreach ($migrations as $migration) {
                $this->rollbackMigration($migration);
                $count++;
            }

            $this->info("Successfully rolled back {$count} migration(s)");
            return 0;
        } catch (\Exception $e) {
            $this->error("Rollback failed: " . $e->getMessage());
            return 1;
        }
    }

    protected function initDatabase(): void
    {
        $driver = $_ENV['DB_DRIVER'] ?? $this->config->get('database.default');
        $config = $this->config->get("database.connections.{$driver}");

        if (empty($config)) {
            throw new RuntimeException("Configuration for database driver '{$driver}' not found");
        }

        Database::init($config);
    }

    protected function migrationsTableExists(): bool
    {
        return Database::schema()->hasTable('migrations');
    }

    protected function getLastBatchNumber(): int
    {
        return (int) Database::table('migrations')->max('batch');
    }

    protected function getMigrationsForBatch(int $batch): array
    {
        return Database::table('migrations')
            ->where('batch', $batch)
            ->orderBy('id', 'desc')
            ->get()
            ->all();
    }

    protected function rollbackMigration(object $migration): void
    {
        $filePath = database_path("migrations" . DIRECTORY_SEPARATOR ."{$migration->migration}.php");

        if (!file_exists($filePath)) {
            throw new RuntimeException("Migration file not found: {$migration->migration}");
        }

        require_once $filePath;

        $className = $this->getMigrationClassName($migration->migration);

        if (!class_exists($className)) {
            throw new RuntimeException("Migration class {$className} not found");
        }

        $instance = new $className();
        $instance->down();

        Database::table('migrations')
            ->where('migration', $migration->migration)
            ->delete();

        $this->line("Rolled back: {$migration->migration}");
    }

    protected function getMigrationClassName(string $migration): string
    {
        $baseName = pathinfo($migration, PATHINFO_FILENAME);
        $parts = explode('_', $baseName);

        $nameParts = array_slice($parts, 4);

        $className = implode('', array_map('ucfirst', $nameParts));

        if (!str_starts_with($className, 'Create')) {
            $className = 'Create'.$className;
        }

        if (!str_ends_with($className, 'Table')) {
            $className .= 'Table';
        }

        return $className;
    }
}