<?php

declare(strict_types=1);

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

    private static ?int $serverTimeDriftMs = null;

    private static float $lastSyncTime = 0;

    private const SYNC_INTERVAL_SECONDS = 300;

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
        $timestamp = $this->getTimestamp();

        $method = strtoupper($request->getMethod());
        $path = '/'.trim($request->getUri()->getPath(), '/');

        $body = $request->getBody();
        $payload = $body->getContents() ?: '';
        $body->rewind();

        $query = $request->getUri()->getQuery();
        if (! empty($query)) {
            $query = '?'.$query;
        }

        $signature = Utility::generateSignature($this->config['secret'], $timestamp, $method, $path, $query, $payload);

        return $request->withHeader('X-BTK-APIKEY', $this->config['api_key'])
            ->withHeader('X-BTK-SIGN', $signature)
            ->withHeader('X-BTK-TIMESTAMP', (string) $timestamp);
    }

    private function getTimestamp(): int
    {
        $now = (int) (microtime(true) * 1000);

        if (self::$serverTimeDriftMs === null || (microtime(true) - self::$lastSyncTime) > self::SYNC_INTERVAL_SECONDS) {
            $endpoint = new SystemEndpoint($this->client);
            $serverTime = (int) $endpoint->serverTimestamp()->throw()->body();
            self::$serverTimeDriftMs = $serverTime - $now;
            self::$lastSyncTime = microtime(true);
        }

        return $now + self::$serverTimeDriftMs;
    }

    /**
     * Reset the cached timestamp drift (useful for testing).
     */
    public static function resetTimestampCache(): void
    {
        self::$serverTimeDriftMs = null;
        self::$lastSyncTime = 0;
    }
}
