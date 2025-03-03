<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleService
{

    private $client;

    public function __construct(array $config = [])
    {
        $this->client = new Client($config);
    }


    public function get(string $url, array $options = [])
    {
        try {
            $response = $this->client->get($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            throw new \Exception("Guzzle GET request failed: " . $e->getMessage());
        }
    }

    public function post(string $url, array $options = [])
    {
        try {
            $response = $this->client->post($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            throw new \Exception("Guzzle POST request failed: " . $e->getMessage());
        }
    }

    // Метод для выполнения PUT-запроса
    public function put(string $url, array $options = [])
    {
        try {
            $response = $this->client->put($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            throw new \Exception("Guzzle PUT request failed: " . $e->getMessage());
        }
    }

    // Метод для выполнения DELETE-запроса
    public function delete(string $url, array $options = [])
    {
        try {
            $response = $this->client->delete($url, $options);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            // Обработка ошибок
            throw new \Exception("Guzzle DELETE request failed: " . $e->getMessage());
        }
    }

    // Метод для получения базового клиента Guzzle (если нужен доступ к другим методам)
    public function getClient(): Client
    {
        return $this->client;
    }
}