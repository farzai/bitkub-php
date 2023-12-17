<?php

namespace Farzai\Bitkub\Requests;

use Farzai\Bitkub\Client;
use Farzai\Bitkub\Contracts\RequestInterceptor;
use Farzai\Bitkub\Utility;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

class GenerateSignatureV3 implements RequestInterceptor
{
    /**
     * The config.
     *
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * The client instance.
     */
    private Client $client;

    /**
     * Create a new client instance.
     */
    public function __construct(array $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Apply the request.
     */
    public function apply(PsrRequestInterface $request): PsrRequestInterface
    {
        $timestamp = (int) Utility::getServerTimestamp($this->client)->format('U');

        $method = strtoupper($request->getMethod());
        $path = '/'.trim($request->getUri()->getPath(), '/');
        $payload = $request->getBody()->getContents() ?: '';

        $query = $request->getUri()->getQuery();
        if (! empty($query)) {
            $query = '?'.$query;
        }

        $signature = Utility::generateSignature($this->config['secret'], $timestamp, $method, $path, $query, $payload);

        return $request->withHeader('X-BTK-APIKEY', $this->config['api_key'])
            ->withHeader('X-BTK-SIGN', $signature)
            ->withHeader('X-BTK-TIMESTAMP', $timestamp);
    }
}
