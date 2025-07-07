<?php

namespace App\Console\Commands;

use App\Core\Command;

class ServeCommand extends Command
{
    protected string $name = 'serve';
    protected string $description = 'Start the PHP development server';

    public function handle(array $arguments = []): int
    {
        $host = '127.0.0.1';
        $port = 8000;
        $publicDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR . 'public');

        // Парсим аргументы
        foreach ($arguments as $arg) {
            if (strpos($arg, '--host=') === 0) {
                $host = substr($arg, 7);
            } elseif (strpos($arg, '--port=') === 0) {
                $port = (int) substr($arg, 7);
            }
        }

        $this->info("Starting PHP development server...");
        $this->line("Document root: {$publicDir}");
        $this->line("URL: http://{$host}:{$port}");
        $this->line("Press Ctrl+C to stop\n");

        passthru("php -S {$host}:{$port} -t {$publicDir}");

        return 0;
    }
}