<?php

namespace App\Core;

class Loader
{

    /**
     * @return void
     */
    public static function register(): void
    {
        self::loadEnv(__DIR__ . '/../../.env');

        spl_autoload_register(function ($class) {
            $file = self::getClassFile($class);
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }

    /**
     * @param string $class
     * @return string
     */
    private static function getClassFile(string $class): string
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

        return $baseDir . $class . '.php';
    }

    /**
     * @param string $path
     * @return void
     */
    public static function loadEnv(string $path): void
    {
        if (!file_exists($path)){
            throw new \RuntimeException(".env file not found at path: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0 || trim($line) === '') {
                continue;
            }

            list($name, $value) = self::parseLine($line);

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    /**
     * @param string $line
     * @return array
     */
    protected static function parseLine(string $line): array
    {
        list($name, $value) = array_map('trim', explode('=', $line, 2));

        $value = self::parseValue($value);

        return [$name, $value];
    }

    /**
     * @param string $value
     * @return string
     */
    private static function parseValue(string $value): string
    {
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            return $matches[1];
        }
        if (preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }

        return $value;
    }
}