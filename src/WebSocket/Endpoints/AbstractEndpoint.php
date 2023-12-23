<?php

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
    public function run()
    {
        $this->websocket->run();
    }
}
