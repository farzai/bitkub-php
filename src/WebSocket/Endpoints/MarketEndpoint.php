<?php

namespace Farzai\Bitkub\WebSocket\Endpoints;

class MarketEndpoint extends AbstractEndpoint
{
    /**
     * Add event listener.
     *
     * @example $websocket->listen('market.trade.thb_btc', function (Message $message) {
     *    echo $message->json('sym').PHP_EOL;
     * });
     *
     * @param  string[]|string  $streamName
     * @param  callable|array<callable>  $listeners
     */
    public function listen($streamName, $listeners)
    {
        if (is_string($streamName)) {
            $streamNames = array_map('trim', explode(',', $streamName));
        } else {
            $streamNames = $streamName;
        }

        foreach ($streamNames as $name) {
            $this->websocket->addListener($this->getStreamName($name), $listeners);
        }

        return $this;
    }

    private function getStreamName(string $streamName): string
    {
        $segments = explode('.', $streamName);

        if (count($segments) === 3) {
            return $streamName;
        }

        if (count($segments) === 2) {
            return 'market.'.$streamName;
        }

        throw new \InvalidArgumentException('Invalid stream name format. Given: '.$streamName);
    }
}
