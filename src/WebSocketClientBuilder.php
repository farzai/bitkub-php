<?php

declare(strict_types=1);

namespace Farzai\Bitkub;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Contracts\WebSocketEngineInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\NullLogger;

final class WebSocketClientBuilder
{
    private const DEFAULT_WS_BASE_URL = 'wss://api.bitkub.com/websocket-api/';

    private ?ClientInterface $client = null;

    private ?WebSocketEngineInterface $engine = null;

    private ?PsrLoggerInterface $logger = null;

    private string $baseUrl = self::DEFAULT_WS_BASE_URL;

    private int $reconnectAttempts = 3;

    private int $reconnectDelayMs = 1000;

    public function __construct() {}

    public static function create(): static
    {
        return new self;
    }

    public function setClient(ClientInterface $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function setEngine(WebSocketEngineInterface $engine): static
    {
        $this->engine = $engine;

        return $this;
    }

    public function setLogger(PsrLoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function setReconnectAttempts(int $reconnectAttempts): static
    {
        if ($reconnectAttempts < 0) {
            throw new \InvalidArgumentException('Reconnect attempts must be greater than or equal to 0.');
        }

        $this->reconnectAttempts = $reconnectAttempts;

        return $this;
    }

    public function setReconnectDelayMs(int $reconnectDelayMs): static
    {
        if ($reconnectDelayMs < 0) {
            throw new \InvalidArgumentException('Reconnect delay must be greater than or equal to 0.');
        }

        $this->reconnectDelayMs = $reconnectDelayMs;

        return $this;
    }

    public function build(): WebSocketClient
    {
        $logger = $this->logger ?? $this->client?->getLogger() ?? new NullLogger;

        $engine = $this->engine ?? new WebSocket\Engine(
            logger: $logger,
            baseUrl: $this->baseUrl,
            reconnectAttempts: $this->reconnectAttempts,
            reconnectDelayMs: $this->reconnectDelayMs,
        );

        return new WebSocketClient(
            engine: $engine,
            client: $this->client,
            logger: $logger,
        );
    }
}
