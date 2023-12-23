<?php

namespace Farzai\Bitkub;

use Farzai\Bitkub\Contracts\ClientInterface;
use Psr\Log\LoggerInterface;

final class WebSocketClient
{
    private ClientInterface $client;

    private Contracts\WebSocketEngineInterface $websocket;

    /**
     * @var array<string, array<mixed>>
     */
    private array $listeners = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->websocket = new WebSocket\Engine($this->getLogger());
    }

    public function getConfig(): array
    {
        return $this->client->getConfig();
    }

    public function getLogger(): LoggerInterface
    {
        return $this->client->getLogger();
    }

    /**
     * Add event listener.
     *
     * @param  callable|array<callable>  $listener
     * @return $this
     */
    public function addListener(string $event, $listener)
    {
        if (! isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event] = array_merge($this->listeners[$event], (array) $listener);

        return $this;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }

    public function run()
    {
        $this->websocket->handle($this->listeners);
    }
}
