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
        $publicPath = dirname(__DIR__, 2) . '/public';
        $fullPath = $publicPath . '/' . ltrim($path, '/');

        if (!file_exists($fullPath)) {
            return $path;
        }

        $version = self::getFileVersion($fullPath);

        return $path . (strpos($path, '?') === false ? '?' : '&') . 'v=' . $version;
    }

    private static function getFileVersion(string $filePath): string
    {
        if (function_exists('md5_file')) {
            return substr(md5_file($filePath), 0, 8);
        }

        return filemtime($filePath);
    }
}