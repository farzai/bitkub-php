<?php

declare(strict_types=1);

use Farzai\Bitkub\Tests\WebSocketTests\MockWebSocketEngine;
use Farzai\Bitkub\WebSocket\Message;

it('dispatches message to correct listener', function () {
    $engine = new MockWebSocketEngine;
    $engine->addMessage('market.trade.thb_btc', ['price' => 100]);

    $received = [];
    $listeners = [
        'market.trade.thb_btc' => [
            function (Message $m) use (&$received) {
                $received[] = $m->json('price');
            },
        ],
    ];

    $engine->handle($listeners);

    expect($received)->toBe([100]);
});

it('dispatches to multiple listeners for same stream', function () {
    $engine = new MockWebSocketEngine;
    $engine->addMessage('market.trade.thb_btc', ['price' => 200]);

    $calls = 0;
    $listeners = [
        'market.trade.thb_btc' => [
            function (Message $m) use (&$calls) {
                $calls++;
            },
            function (Message $m) use (&$calls) {
                $calls++;
            },
        ],
    ];

    $engine->handle($listeners);

    expect($calls)->toBe(2);
});

it('ignores messages for unregistered streams', function () {
    $engine = new MockWebSocketEngine;
    $engine->addMessage('market.trade.thb_eth', ['price' => 300]);

    $received = [];
    $listeners = [
        'market.trade.thb_btc' => [
            function (Message $m) use (&$received) {
                $received[] = $m->json('price');
            },
        ],
    ];

    $engine->handle($listeners);

    expect($received)->toBe([]);
});

it('dispatches messages with pre-decoded data', function () {
    $engine = new MockWebSocketEngine;
    $engine->addMessage('market.trade.thb_btc', ['price' => 500, 'vol' => 1.5]);

    $received = null;
    $listeners = [
        'market.trade.thb_btc' => [
            function (Message $m) use (&$received) {
                $received = $m->json();
            },
        ],
    ];

    $engine->handle($listeners);

    expect($received)->toHaveKey('price', 500);
    expect($received)->toHaveKey('vol', 1.5);
    expect($received)->toHaveKey('stream', 'market.trade.thb_btc');
});
