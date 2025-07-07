<?php

namespace App\Services;

use App\Core\HttpClientCore;
use Exception;
use GuzzleHttp\Client;

class StaticHttpClient extends HttpClientCore
{
    private static ?Client $sharedClient = null;

    public function __construct(array $config = [])
    {
        if (self::$sharedClient === null) {
            self::$sharedClient = new Client($config);
        }
        $this->client = self::$sharedClient;
    }

    /**
     * @throws Exception
     */
    public static function get(string $url, array $options = [], array $config = []): string
    {
        return (new self($config))->execute('get', $url, $options);
    }

    /**
     * @throws Exception
     */
    public static function post(string $url, array $options = [], array $config = []): string
    {
        return (new self($config))->execute('post', $url, $options);
    }

    /**
     * @throws Exception
     */
    public static function put(string $url, array $options = [], array $config = []): string
    {
        return (new self($config))->execute('put', $url, $options);
    }

    /**
     * @throws Exception
     */
    public static function delete(string $url, array $options = [], array $config = []): string
    {
        return (new self($config))->execute('delete', $url, $options);
    }
}