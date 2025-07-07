<?php

namespace App\Console\Commands;

use App\Core\Command;

class MakeModelCommand extends Command
{
    protected string $name = 'make:model';
    protected string $description = 'Create a new model class';

    public function handle(array $arguments): int
    {
        if (empty($arguments)) {
            $this->error('Please specify model name');
            return 1;
        }

        $name = ucfirst($arguments[0]);
        $filePath = app_path("Models" . DIRECTORY_SEPARATOR . "{$name}.php");

        if (file_exists($filePath)) {
            $this->error("Model {$name} already exists!");
            return 1;
        }

        $stub = $this->getStubContent($name);

        if (file_put_contents($filePath, $stub)) {
            $this->info("Model created: {$name}");
            return 0;
        }

        $this->error("Failed to create model");
        return 1;
    }

    protected function getStubContent(string $className): string
    {
        $stub = file_get_contents(__DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'model.stub');
        return str_replace('{{ClassName}}', $className, $stub);
    }
}