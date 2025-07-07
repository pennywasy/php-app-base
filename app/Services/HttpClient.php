<?php

namespace App\Services;

use App\Core\HttpClientCore;
use Exception;

class HttpClient extends HttpClientCore
{
    /**
     * @throws Exception
     */
    public function get(string $url, array $options = []): string
    {
        return $this->execute('get', $url, $options);
    }

    /**
     * @throws Exception
     */
    public function post(string $url, array $options = []): string
    {
        return $this->execute('post', $url, $options);
    }

    /**
     * @throws Exception
     */
    public function put(string $url, array $options = []): string
    {
        return $this->execute('put', $url, $options);
    }

    /**
     * @throws Exception
     */
    public function delete(string $url, array $options = []): string
    {
        return $this->execute('delete', $url, $options);
    }
}