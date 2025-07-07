<?php

namespace App\Services;

use App\Core\Logger;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleService
{

    private Client $client;

    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }


    /**
     * @throws Exception
     */
    public function get(string $url, array $options = []): string
    {
        try {
            $response = $this->client->get($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            Logger::error("Guzzle GET request failed: " . $e->getMessage());
            throw new Exception("Guzzle GET request failed: " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function post(string $url, array $options = []): string
    {
        try {
            $response = $this->client->post($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            Logger::error("Guzzle POST request failed: " . $e->getMessage());
            throw new Exception("Guzzle POST request failed: " . $e->getMessage());
        }
    }

    // Метод для выполнения PUT-запроса

    /**
     * @throws Exception
     */
    public function put(string $url, array $options = []): string
    {
        try {
            $response = $this->client->put($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            Logger::error("Guzzle PUT request failed: " . $e->getMessage());
            throw new Exception("Guzzle PUT request failed: " . $e->getMessage());
        }
    }

    // Метод для выполнения DELETE-запроса

    /**
     * @throws Exception
     */
    public function delete(string $url, array $options = []): string
    {
        try {
            $response = $this->client->delete($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            Logger::error("Guzzle DELETE request failed: " . $e->getMessage());
            throw new Exception("Guzzle DELETE request failed: " . $e->getMessage());
        }
    }

    // Метод для получения базового клиента Guzzle (если нужен доступ к другим методам)
    public function getClient(): Client
    {
        return $this->client;
    }
}