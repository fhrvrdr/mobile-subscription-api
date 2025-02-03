<?php

namespace App\Services\Payment\Client;

use App\DTO\Application\ProviderCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

final readonly class GoogleClient
{
    private Client $client;

    public function __construct(ProviderCredentials $credentials)
    {
        $this->client = new Client([
            'base_uri' => config('services.google.api_url'),
            'headers' => [
                'Authorization' => 'Basic '.base64_encode($credentials->username.':'.$credentials->password),
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function post(string $url, array $data): ResponseInterface
    {
        return $this->client->post($url, ['json' => $data]);
    }
}
