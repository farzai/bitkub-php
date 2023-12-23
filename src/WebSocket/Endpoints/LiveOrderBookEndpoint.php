<?php

namespace Farzai\Bitkub\WebSocket\Endpoints;

class LiveOrderBookEndpoint extends AbstractEndpoint
{
    /**
     * Add event listener.
     *
     * @example $websocket->listen('thb_btc', function (Message $message) {
     *    echo $message->json('sym').PHP_EOL;
     * });
     *
     * @param  callable|array<callable>  $listeners
     */
    public function listen(string $symbol, $listeners)
    {
        $this->websocket->addListener('orderbook/'.$symbol, $listeners);

        return $this;
    }
}
