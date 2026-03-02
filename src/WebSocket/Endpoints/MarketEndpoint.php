<?php

declare(strict_types=1);

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
     * @param  callable|array<callable(\Farzai\Bitkub\WebSocket\Message): void>  $listeners
     */
    public function listen(string|array $streamNames, callable|array $listeners): static
    {
        $streamNames = $this->normalizeStreamNames($streamNames);

        $this->getLogger()->debug('Subscribing to streams: '.implode(', ', $streamNames));

        foreach ($streamNames as $name) {
            $this->websocket->addListener($name, $listeners);
        }

        return $this;
    }

    /**
     * Normalize stream names.
     *
     * @param  string[]|string  $streamNames
     * @return string[]
     */
    private function normalizeStreamNames(string|array $streamNames): array
    {
        if (is_string($streamNames)) {
            $streamNames = explode(',', $streamNames);
        }

        $streamNames = array_filter(
            array_map('trim', $streamNames),
            fn (string $name): bool => ! empty($name),
        );

        $streamNames = array_map(
            fn (string $name): string => $this->getStreamName($name),
            $streamNames,
        );

        return $streamNames;
    }

    private function getStreamName(string $streamName): string
    {
        $streamName = 'market.'.preg_replace('/^market\./', '', $streamName);

        if (preg_match('/^market\.[a-z]+\.[a-z0-9_]+$/i', $streamName)) {
            return $streamName;
        }

        throw new \InvalidArgumentException('Invalid stream name format. Given: '.$streamName);
    }
}
