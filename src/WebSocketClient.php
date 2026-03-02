<?php

declare(strict_types=1);

namespace Farzai\Bitkub;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Contracts\WebSocketEngineInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class WebSocketClient
{
    /**
     * @var array<string, array<callable(\Farzai\Bitkub\WebSocket\Message): void>>
     */
    private array $listeners = [];

    private ?WebSocket\Endpoints\MarketEndpoint $marketEndpoint = null;

    private ?WebSocket\Endpoints\LiveOrderBookEndpoint $liveOrderBookEndpoint = null;

    public function __construct(
        private WebSocketEngineInterface $engine,
        private ?ClientInterface $client = null,
        private ?LoggerInterface $logger = null,
    ) {}

    public function getConfig(): array
    {
        return $this->client?->getConfig() ?? [];
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger ?? $this->client?->getLogger() ?? new NullLogger;
    }

    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    public function market(): WebSocket\Endpoints\MarketEndpoint
    {
        return $this->marketEndpoint ??= new WebSocket\Endpoints\MarketEndpoint($this);
    }

    public function liveOrderBook(): WebSocket\Endpoints\LiveOrderBookEndpoint
    {
        return $this->liveOrderBookEndpoint ??= new WebSocket\Endpoints\LiveOrderBookEndpoint($this);
    }

    /**
     * Add event listener.
     *
     * @param  callable|array<callable(\Farzai\Bitkub\WebSocket\Message): void>  $listener
     */
    public function addListener(string $event, callable|array $listener): static
    {
        if (! isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event] = array_merge($this->listeners[$event], (array) $listener);

        return $this;
    }

    /**
     * @return array<string, array<callable(\Farzai\Bitkub\WebSocket\Message): void>>
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function run(): void
    {
        $this->engine->handle($this->listeners);
    }
}
