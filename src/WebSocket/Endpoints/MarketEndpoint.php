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
     * @param  string[]|string  $streamNames
     * @param  callable|array<callable>  $listeners
     */
    public function listen($streamNames, $listeners)
    {
        $streamNames = $this->normalizeStreamNames($streamNames);

        $this->getLogger()->debug('Add event listener for stream: '.implode(', ', $streamNames));

        foreach ($streamNames as $name) {
            $this->websocket->addListener($name, $listeners);
        }

        return $this;
    }

    /**
     * Normalize stream names.
     *
     * @param  string[]|string  $streamNames
     */
    private function normalizeStreamNames($streamNames): array
    {
        if (is_string($streamNames)) {
            $streamNames = explode(',', $streamNames);
        }

        $streamNames = array_filter(array_map('trim', $streamNames), function ($streamName) {
            return ! empty($streamName);
        });

        $streamNames = array_map(fn ($streamName) => $this->getStreamName($streamName), $streamNames);

        return $streamNames;
    }

    private function getStreamName(string $streamName): string
    {
        $streamName = 'market.'.preg_replace('/^market\./', '', $streamName);

        // Validate stream name format.
        if (substr_count($streamName, '.') === 2) {
            return $streamName;
        }

        throw new \InvalidArgumentException('Invalid stream name format. Given: '.$streamName);
    }
}
