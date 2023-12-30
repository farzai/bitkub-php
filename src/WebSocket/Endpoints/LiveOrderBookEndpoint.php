<?php

namespace Farzai\Bitkub\WebSocket\Endpoints;

use Farzai\Bitkub\Endpoints as RestApiEndpoints;

class LiveOrderBookEndpoint extends AbstractEndpoint
{
    /**
     * Add event listener.
     *
     * @example $websocket->listen('thb_btc', function (Message $message) {
     *    echo $message->json('sym');
     * });
     *
     * @param  string|int  $symbol Symbol name or id.
     * @param  callable|array<callable>  $listeners
     */
    public function listen($symbol, $listeners)
    {
        // Check if symbol is numeric.
        if (! is_numeric($symbol)) {

            $this->getLogger()->debug('Find symbol id by name: '.$symbol);

            // Find symbol id by name.
            $market = new RestApiEndpoints\MarketEndpoint($this->websocket->getClient());

            foreach ($market->symbols()->throw()->json('result') as $item) {
                if ($item['symbol'] === strtoupper(trim($symbol))) {
                    $symbol = $item['id'];

                    $this->getLogger()->debug('Found symbol id: '.$symbol);
                    break;
                }
            }

            if (! is_numeric($symbol)) {
                $this->getLogger()->debug('Invalid symbol name. Given: '.$symbol);

                throw new \InvalidArgumentException('Invalid symbol name. Given: '.$symbol);
            }
        }

        $this->websocket->addListener('orderbook/'.$symbol, $listeners);

        return $this;
    }
}
