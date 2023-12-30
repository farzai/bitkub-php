<?php

namespace Farzai\Bitkub\Requests;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Contracts\RequestInterceptor;
use Farzai\Bitkub\Endpoints\SystemEndpoint;
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
    private ClientInterface $client;

    /**
     * Create a new client instance.
     */
    public function __construct(array $config, ClientInterface $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Apply the request.
     */
    public function apply(PsrRequestInterface $request): PsrRequestInterface
    {
        $endpoint = new SystemEndpoint($this->client);

        $timestamp = (int) $endpoint->serverTimestamp()->throw()->body();

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
