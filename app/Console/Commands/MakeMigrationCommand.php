<?php

namespace App\Console\Commands;

use App\Core\Command;
use App\Core\Filesystem;

class MakeMigrationCommand extends Command
{
    protected string $name = 'make:migration';
    protected string $description = 'Create a new migration file';

    public function handle(array $arguments): int
    {
        if (empty($arguments)) {
            $this->error('Please specify migration name');
            return 1;
        }

        $name = $this->normalizeInput($arguments[0]);
        $className = $this->generateClassName($name);
        $tableName = $this->generateTableName($name);
        $fileName = $this->generateFileName($name);

        $filePath = database_path('migrations'. DIRECTORY_SEPARATOR .$fileName);

        if (!is_dir(database_path('migrations'))) {
            mkdir(database_path('migrations'), 0755, true);
        }

        $stub = $this->getStubContent($className, $tableName);

        if (file_put_contents($filePath, $stub)) {
            $this->info("Migration created successfully!");
            $this->line("File: {$fileName}");
            $this->line("Class: {$className}");
            $this->line("Table: {$tableName}");
            return 0;
        }

        $this->error("Failed to create migration");
        return 1;
    }

    protected function normalizeInput(string $input): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }

    protected function generateClassName(string $name): string
    {
        $cleanName = preg_replace(['/^create_/i', '/_table$/i'], '', $name);

        $className = str_replace('_', '', ucwords($cleanName, '_'));

        return 'Create' . $className . 'Table';
    }

    protected function generateTableName(string $name): string
    {
        $table = preg_replace(['/^create_/i', '/_table$/i'], '', $name);

        return strtolower($table);
    }

    protected function generateFileName(string $name): string
    {
        $fileName = strtolower($name);
        if (!str_starts_with($fileName, 'create_')) {
            $fileName = 'create_' . $fileName;
        }
        if (!str_ends_with($fileName, '_table')) {
            $fileName .= '_table';
        }

        return date('Y_m_d_His') . '_' . $fileName . '.php';
    }

    protected function getStubContent(string $className, string $tableName): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'migration.stub';

        if (!file_exists($stubPath)) {
            throw new \RuntimeException('Migration stub file not found at: ' . $stubPath);
        }

        $stub = file_get_contents($stubPath);

        return str_replace(
            ['{{ClassName}}', '{{TableName}}'],
            [$className, $tableName],
            $stub
        );
    }
}