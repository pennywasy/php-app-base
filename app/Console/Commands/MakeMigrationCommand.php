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

        $name = $this->getMigrationName($arguments[0]);
        $fileName = date('Y_m_d_His').'_'.$name.'.php';
        $filePath = database_path('migrations' . DIRECTORY_SEPARATOR . $fileName);

        $stub = $this->getStubContent($name);

        if (file_put_contents($filePath, $stub)) {
            $this->info("Migration created: {$fileName}");
            return 0;
        }

        $this->error("Failed to create migration");
        return 1;
    }

    protected function getMigrationName(string $input): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }

    protected function getStubContent(string $className): string
    {
        $stub = file_get_contents(__DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'migration.stub');
        return str_replace('{{ClassName}}', 'Create'.ucfirst($className).'Table', $stub);
    }
}