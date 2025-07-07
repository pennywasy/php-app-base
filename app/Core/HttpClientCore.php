<?php

namespace App\Core;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Core\Logger;

abstract class HttpClientCore
{
    protected Client $client;

    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }

    /**
     * @throws Exception
     */
    protected function execute(string $method, string $url, array $options = []): string
    {
        try {
            $response = $this->client->$method($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            $error = "HTTP {$method} request failed: " . $e->getMessage();
            Logger::error($error);
            throw new Exception($error);
        }
    }
}