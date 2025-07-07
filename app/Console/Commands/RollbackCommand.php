<?php

namespace App\Console\Commands;

use App\Core\Command;
use App\Core\Config;
use App\Core\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class RollbackCommand extends Command
{
    protected string $name = 'migrate:rollback';
    protected string $description = 'Rollback the last database migration';

    public function handle(array $arguments): int
    {
        $this->initDatabase();

        $migrations = $this->getMigrationFiles();
        $count = 0;

        if (!empty($migrations)) {
            $file = end($migrations);
            $className = $this->getClassNameFromFile($file);
            require_once $file;

            $migration = new $className();
            $migration->down();

            $this->line("Rolled back: {$className}");
            $count++;
        }

        $this->info("Successfully rolled back {$count} migration");
        return 0;
    }

    protected function initDatabase(): void
    {
        $dbConfig = Config::getInstance()->get('database');
        $dbDriver = $_ENV['DB_DRIVER'] ?? $dbConfig['default'];
        $dbHost = $dbConfig[$dbDriver];
        $capsule = new Capsule;
        $capsule->addConnection($dbHost);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    protected function getMigrationFiles(): array
    {
        $migrationPath = database_path('migrations');
        if (!is_dir($migrationPath)) {
            return [];
        }

        $files = [];
        foreach (scandir($migrationPath) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = $migrationPath . DIRECTORY_SEPARATOR . $file;
            }
        }

        sort($files);
        return $files;
    }

    protected function getClassNameFromFile(string $file): string
    {
        $contents = file_get_contents($file);
        preg_match('/class\s+(\w+)\s+extends/', $contents, $matches);
        return $matches[1];
    }
}