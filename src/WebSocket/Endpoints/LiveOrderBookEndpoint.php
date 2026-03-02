<?php

declare(strict_types=1);

namespace Farzai\Bitkub\WebSocket\Endpoints;

use Farzai\Bitkub\Endpoints as RestApiEndpoints;

class LiveOrderBookEndpoint extends AbstractEndpoint
{
    /** @var array<string, int>|null */
    private static ?array $symbolMap = null;

    /**
     * Add event listener.
     *
     * @example $websocket->listen('thb_btc', function (Message $message) {
     *    echo $message->json('sym');
     * });
     *
     * @param  string|int  $symbol  Symbol name or id.
     * @param  callable|array<callable>  $listeners
     */
    public function listen($symbol, $listeners)
    {
        if (! is_numeric($symbol)) {
            $symbol = $this->resolveSymbolId((string) $symbol);
        }

        $this->websocket->addListener('orderbook/'.$symbol, $listeners);

        return $this;
    }

    private function resolveSymbolId(string $symbol): int
    {
        if (self::$symbolMap === null) {
            self::$symbolMap = [];
            $market = new RestApiEndpoints\MarketEndpoint($this->websocket->getClient());

            foreach ($market->symbols()->throw()->json('result') as $item) {
                self::$symbolMap[strtoupper($item['symbol'])] = $item['id'];
            }
        }

        $key = strtoupper(trim($symbol));
        if (! isset(self::$symbolMap[$key])) {
            throw new \InvalidArgumentException('Invalid symbol name. Given: '.$symbol);
        }

        return self::$symbolMap[$key];
    }

    /**
     * Reset the cached symbol map (useful for testing).
     */
    public static function resetSymbolMapCache(): void
    {
        self::$symbolMap = null;
    }
}
