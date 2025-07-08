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
    protected string $description = 'Rollback database migrations';
    protected array $arguments = [
        ['steps', 'Number of batches to rollback', 1]
    ];

    private Config $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    public function handle(array $arguments): int
    {
        try {
            $steps = (int)($arguments['steps'] ?? 1);
            $this->initDatabase();
            $this->info("Using database driver: " . Database::getDriverName());

            if (!$this->migrationsTableExists()) {
                $this->error("Migrations table does not exist");
                return 1;
            }

            $totalRolledBack = 0;

            for ($i = 0; $i < $steps; $i++) {
                $batch = $this->getLastBatchNumber();

                // Если больше нечего откатывать
                if ($batch === 0) {
                    break;
                }

                $migrations = $this->getMigrationsForBatch($batch);

                if (empty($migrations)) {
                    $this->info("Nothing to rollback in batch {$batch}");
                    break;
                }

                $count = 0;
                foreach ($migrations as $migration) {
                    $this->rollbackMigration($migration);
                    $count++;
                }

                $totalRolledBack += $count;
                $this->info("Rolled back {$count} migration(s) from batch {$batch}");
            }

            if ($totalRolledBack > 0) {
                $this->info("Successfully rolled back {$totalRolledBack} migration(s)");
            } else {
                $this->info("Nothing to rollback");
            }

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
            $className = $this->findMigrationClass($filePath);
            if ($className === null) {
                throw new RuntimeException("Migration class not found in file {$filePath}");
            }
        }

        $instance = new $className();
        $instance->down();

        Database::table('migrations')
            ->where('migration', $migration->migration)
            ->delete();

        $this->line("Rolled back: {$migration->migration} ({$className})");
    }

    protected function getMigrationClassName(string $migration): string
    {
        $baseName = pathinfo($migration, PATHINFO_FILENAME);
        $parts = explode('_', $baseName);

        $nameParts = array_slice($parts, 4);

        $isUpdate = str_starts_with(strtolower(implode('_', $nameParts)), 'update_') ||
            str_contains(strtolower(implode('_', $nameParts)), '_update');

        $className = implode('', array_map('ucfirst', $nameParts));
        $className = preg_replace('/^(Create|Update)/', '', $className);
        $className = preg_replace('/Table$/', '', $className);

        return $isUpdate ? 'Update' . $className . 'Table' : 'Create' . $className . 'Table';
    }

    protected function findMigrationClass(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if (preg_match('/class\s+([^\s]+)\s+extends/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
}