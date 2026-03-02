<?php

declare(strict_types=1);

use Farzai\Bitkub\Tests\WebSocketTests\MockWebSocketEngine;
use Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint;
use Farzai\Bitkub\WebSocketClient;

it('throws InvalidArgumentException for invalid stream name', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new MarketEndpoint($client);

    $endpoint->listen('invalid!!!', function () {});
})->throws(\InvalidArgumentException::class, 'Invalid stream name format.');

it('normalizes stream with and without market. prefix', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new MarketEndpoint($client);

    $endpoint->listen('trade.thb_btc', function () {});
    $endpoint->listen('market.trade.thb_btc', function () {});

    $listeners = $client->getListeners();
    expect($listeners)->toHaveCount(1);
    expect($listeners['market.trade.thb_btc'])->toHaveCount(2);
});

it('listen returns static for chaining', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new MarketEndpoint($client);

    $result = $endpoint->listen('trade.thb_btc', function () {});

    expect($result)->toBe($endpoint);
});

it('supports comma-separated stream names as string', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new MarketEndpoint($client);

    $endpoint->listen('trade.thb_btc, trade.thb_eth', function () {});

    $listeners = $client->getListeners();
    expect($listeners)->toHaveCount(2);
    expect($listeners)->toHaveKey('market.trade.thb_btc');
    expect($listeners)->toHaveKey('market.trade.thb_eth');
});
