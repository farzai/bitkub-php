<?php

declare(strict_types=1);

namespace Farzai\Bitkub\WebSocket\Endpoints;

use Farzai\Bitkub\WebSocketClient;

abstract class AbstractEndpoint
{
    protected WebSocketClient $websocket;

    public function __construct(WebSocketClient $websocket)
    {
        $this->websocket = $websocket;
    }

    /**
     * Run the websocket.
     */
    public function run(): void
    {
        $this->websocket->run();
    }

    protected function getLogger(): \Psr\Log\LoggerInterface
    {
        return $this->websocket->getLogger();
    }
}
