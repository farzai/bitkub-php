<?php

namespace Farzai\Bitkub\Requests;

use Farzai\Bitkub\Contracts\RequestInterceptor;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

class GenerateSignatureV3 implements RequestInterceptor
{
    /**
     * The config.
     *
     * @var array<string, mixed>
     */
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function generate($timestamp, $method, $path, $query, $payload)
    {
        $message = sprintf('%s%s%s%s%s', $timestamp, $method, $path, $query, $payload);
        $signature = hash_hmac('sha256', $message, $this->config['secret']);

        return $signature;
    }

    /**
     * Apply the request.
     */
    public function apply(PsrRequestInterface $request): PsrRequestInterface
    {
        $timestamp = (int) (microtime(true) * 1000);
        $method = strtoupper($request->getMethod());
        $path = '/'.trim($request->getUri()->getPath(), '/');
        $payload = $request->getBody()->getContents() ?: '';

        $query = $request->getUri()->getQuery();
        if (! empty($query)) {
            $query = '?'.$query;
        }

        $signature = $this->generate($timestamp, $method, $path, $query, $payload);

        return $request->withHeader('X-BTK-APIKEY', $this->config['api_key'])
            ->withHeader('X-BTK-SIGN', $signature)
            ->withHeader('X-BTK-TIMESTAMP', $timestamp);
    }
}
