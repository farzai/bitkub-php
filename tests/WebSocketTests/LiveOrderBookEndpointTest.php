<?php

declare(strict_types=1);

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Bitkub\Tests\WebSocketTests\MockWebSocketEngine;
use Farzai\Bitkub\WebSocket\Endpoints\LiveOrderBookEndpoint;
use Farzai\Bitkub\WebSocketClient;

it('throws RuntimeException when no REST client and symbol name used', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen('thb_btc', function () {});
})->throws(\RuntimeException::class, 'A REST client is required to resolve symbol names.');

it('accepts numeric symbol ID without REST client', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen(1, function () {});

    expect($client->getListeners())->toHaveKey('orderbook/1');
});

it('caches symbol map per instance not globally', function () {
    $symbolResponseBody = [
        'error' => 0,
        'result' => [
            ['id' => 1, 'symbol' => 'THB_BTC', 'info' => 'Thai Baht to Bitcoin'],
            ['id' => 2, 'symbol' => 'THB_ETH', 'info' => 'Thai Baht to Ethereum'],
        ],
    ];

    // First instance
    $httpClient1 = MockHttpClient::make()
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)));

    $baseClient1 = ClientBuilder::create()
        ->setCredentials('key', 'secret')
        ->setHttpClient($httpClient1)
        ->build();

    $engine1 = new MockWebSocketEngine;
    $wsClient1 = new WebSocketClient($engine1, $baseClient1);
    $endpoint1 = new LiveOrderBookEndpoint($wsClient1);
    $endpoint1->listen('thb_btc', function () {});

    // Second instance — should make its own HTTP call (not use static cache)
    $httpClient2 = MockHttpClient::make()
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)));

    $baseClient2 = ClientBuilder::create()
        ->setCredentials('key', 'secret')
        ->setHttpClient($httpClient2)
        ->build();

    $engine2 = new MockWebSocketEngine;
    $wsClient2 = new WebSocketClient($engine2, $baseClient2);
    $endpoint2 = new LiveOrderBookEndpoint($wsClient2);
    $endpoint2->listen('thb_btc', function () {});

    // Both should have their own listeners
    expect($wsClient1->getListeners())->toHaveKey('orderbook/1');
    expect($wsClient2->getListeners())->toHaveKey('orderbook/1');
});

it('throws for unknown symbol name', function () {
    $symbolResponseBody = [
        'error' => 0,
        'result' => [
            ['id' => 1, 'symbol' => 'THB_BTC', 'info' => 'Thai Baht to Bitcoin'],
        ],
    ];

    $httpClient = MockHttpClient::make()
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)));

    $baseClient = ClientBuilder::create()
        ->setCredentials('key', 'secret')
        ->setHttpClient($httpClient)
        ->build();

    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine, $baseClient);
    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen('thb_unknown', function () {});
})->throws(\InvalidArgumentException::class, 'Invalid symbol name. Given: thb_unknown');

it('listen returns static for chaining', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);
    $endpoint = new LiveOrderBookEndpoint($client);

    $result = $endpoint->listen(1, function () {});

    expect($result)->toBe($endpoint);
});
