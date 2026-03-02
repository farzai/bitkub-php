<?php

declare(strict_types=1);

namespace Farzai\Bitkub\WebSocket\Endpoints;

use Farzai\Bitkub\Endpoints as RestApiEndpoints;

class LiveOrderBookEndpoint extends AbstractEndpoint
{
    /** @var array<string, int>|null */
    private ?array $symbolMap = null;

    /**
     * Add event listener.
     *
     * @example $websocket->listen('thb_btc', function (Message $message) {
     *    echo $message->json('sym');
     * });
     *
     * @param  string|int  $symbol  Symbol name or id.
     * @param  callable|array<callable(\Farzai\Bitkub\WebSocket\Message): void>  $listeners
     */
    public function listen(string|int $symbol, callable|array $listeners): static
    {
        if (! is_numeric($symbol)) {
            $symbol = $this->resolveSymbolId((string) $symbol);
        }

        $this->websocket->addListener('orderbook/'.$symbol, $listeners);

        return $this;
    }

    private function resolveSymbolId(string $symbol): int
    {
        $client = $this->websocket->getClient();
        if ($client === null) {
            throw new \RuntimeException('A REST client is required to resolve symbol names. Use numeric symbol IDs or set a client via WebSocketClientBuilder::setClient().');
        }

        if ($this->symbolMap === null) {
            $this->symbolMap = [];
            $market = new RestApiEndpoints\MarketEndpoint($client);

            foreach ($market->symbols()->throw()->json('result') as $item) {
                $this->symbolMap[strtoupper($item['symbol'])] = $item['id'];
            }
        }

        $key = strtoupper(trim($symbol));
        if (! isset($this->symbolMap[$key])) {
            throw new \InvalidArgumentException('Invalid symbol name. Given: '.$symbol);
        }

        return $this->symbolMap[$key];
    }
}
