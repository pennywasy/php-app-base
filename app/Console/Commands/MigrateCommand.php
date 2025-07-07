<?php

namespace App\Console\Commands;

use App\Core\Command;
use App\Core\Config;
use App\Core\Database;
use Illuminate\Support\Carbon;
use RuntimeException;

class MigrateCommand extends Command
{
    protected string $name = 'migrate';
    protected string $description = 'Run database migrations';

    private Config $config;

    public function __construct()
    {
        $this->config = Config::getInstance();
    }

    public function handle(array $arguments): int
    {
        try {
            $this->initDatabase();

            if (!Database::schema()->hasTable('migrations')) {
                $this->createMigrationsTable();
                $this->info('Created migrations table');
            }

            $migrations = $this->getPendingMigrations();

            if (empty($migrations)) {
                $this->info('No migrations to run');
                return 0;
            }

            $batch = $this->getNextBatchNumber();
            $count = 0;

            foreach ($migrations as $migration) {
                $this->runMigration($migration, $batch);
                $count++;
            }

            $this->info("Successfully migrated {$count} file(s)");
            return 0;
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            return 1;
        }
    }

    protected function initDatabase(): void
    {
        // Получаем драйвер из .env или конфига
        $driver = $_ENV['DB_DRIVER'] ?? $this->config->get('database.default');
        $config = $this->config->get("database.connections.{$driver}");

        if (empty($config)) {
            throw new RuntimeException("Configuration for database driver '{$driver}' not found");
        }

        // Инициализируем подключение к БД
        Database::init($config);
    }

    protected function migrationsTableExists(): bool
    {
        return Database::schema()->hasTable('migrations');
    }

    protected function createMigrationsTable(): void
    {
        Database::schema()->create('migrations', function ($table) {
            $table->increments('id');
            $table->string('migration')->unique();
            $table->integer('batch');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    protected function getPendingMigrations(): array
    {
        $ranMigrations = Database::table('migrations')
            ->pluck('migration')
            ->all();

        $migrationFiles = $this->getMigrationFiles();

        return array_diff($migrationFiles, $ranMigrations);
    }

    protected function getMigrationFiles(): array
    {
        $path = database_path('migrations');

        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        foreach (scandir($path) as $file) {
            if (preg_match('/^(\d+_\d+_\d+_\d+_.+?)\.php$/', $file, $matches)) {
                $files[] = $matches[1];
            }
        }

        sort($files);
        return $files;
    }

    protected function getMigrationClassName(string $migration): string
    {
        // Пример: 2023_05_20_000000_create_users_table.php → CreateUsersTable
        $baseName = pathinfo($migration, PATHINFO_FILENAME);
        $parts = explode('_', $baseName);

        // Пропускаем временную метку (первые 4 части)
        $nameParts = array_slice($parts, 4);

        // Склеиваем оставшиеся части и преобразуем в CamelCase
        $className = implode('', array_map('ucfirst', $nameParts));

        // Добавляем префикс Create если его нет
        if (!str_starts_with($className, 'Create')) {
            $className = 'Create'.$className;
        }

        // Добавляем суффикс Table если его нет
        if (!str_ends_with($className, 'Table')) {
            $className .= 'Table';
        }

        return $className;
    }

    protected function runMigration(string $migration, int $batch): void
    {
        $filePath = database_path("migrations" . DIRECTORY_SEPARATOR . "{$migration}.php");

        if (!file_exists($filePath)) {
            throw new RuntimeException("Migration file not found: {$migration}");
        }

        require_once $filePath;

        $className = $this->getMigrationClassName($migration);

        if (!class_exists($className)) {
            throw new RuntimeException("Migration class {$className} not found in file {$filePath}");
        }

        $instance = new $className();
        $instance->up();

        Database::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->line("Migrated: {$migration}");
    }


    protected function getNextBatchNumber(): int
    {
        $lastBatch = Database::table('migrations')->max('batch');
        return (int)$lastBatch + 1;
    }
}