<?php

namespace App\Console;

use App\Core\Command;
use Exception;

class Kernel
{
    protected array $commands = [
        Commands\MakeControllerCommand::class,
        Commands\ServeCommand::class
    ];

    public function handle(array $argv): int
    {
        $commandName = $argv[1] ?? null;

        if (!$commandName) {
            $this->showAvailableCommands();
            return 1;
        }

        foreach ($this->getCommands() as $command) {
            if ($command->getName() === $commandName) {
                $arguments = array_slice($argv, 2);
                return $command->handle($arguments);
            }
        }

        $this->showError("Команда {$commandName} не найдена");
        $this->showAvailableCommands();
        return 1;
    }

    protected function getCommands(): array
    {
        return array_map(fn($class) => new $class(), $this->commands);
    }

    protected function showAvailableCommands(): void
    {
        echo "Доступные команды:\n";
        foreach ($this->getCommands() as $command) {
            echo "  \033[32m" . str_pad($command->getName(), 20) . "\033[0m" . $command->getDescription() . "\n";
        }
    }

    protected function showError(string $message): void
    {
        echo "\033[31mОшибка: \033[0m{$message}\n";
    }
}