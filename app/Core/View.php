<?php

namespace App\Core;

use Exception;

class View
{
    /**
     * @param string $templatePath
     * @param array $data
     * @return string
     * @throws Exception
     */
    public static function render(string $templatePath, array $data = []): string
    {
        $fullPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'resources'
            . DIRECTORY_SEPARATOR
            . 'views'
            . DIRECTORY_SEPARATOR
            . ltrim($templatePath, '/');

        if (!file_exists($fullPath)) {
            throw new Exception("Template not found: {$templatePath}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $fullPath;
        return ob_get_clean();
    }

    /**
     * @param string $path
     * @return string
     */
    public static function asset(string $path): string
    {
        $fullPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . $path;

        if (!file_exists($fullPath)) {
            return $path;
        }

        $hash = md5_file($fullPath);
        return $path . '?v=' . substr($hash, 0, 8);
    }
}