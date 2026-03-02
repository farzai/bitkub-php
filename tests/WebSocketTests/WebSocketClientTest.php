<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Bitkub\Tests\WebSocketTests\MockWebSocketEngine;
use Farzai\Bitkub\WebSocket\Endpoints\AbstractEndpoint;
use Farzai\Bitkub\WebSocket\Endpoints\LiveOrderBookEndpoint;
use Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint;
use Farzai\Bitkub\WebSocketClient;
use Farzai\Bitkub\WebSocketClientBuilder;

it('should create new instance of WebSocketClient via builder', function () {
    $baseClient = ClientBuilder::create()
        ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
        ->build();

    $ws = WebSocketClientBuilder::create()
        ->setClient($baseClient)
        ->build();

    expect($ws)->toBeInstanceOf(WebSocketClient::class);
    expect($ws->getConfig())->toBe($baseClient->getConfig());
});

it('should add listener success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $client->addListener('market.trade.thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(1);

    $client->addListener('market.trade.thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(2);
});

it('should add listener with array', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $client->addListener('market.trade.thb_btc', [
        function () {
            //
        },
        function () {
            //
        },
    ]);

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(2);

    $client->addListener('market.trade.thb_btc', [
        function () {
            //
        },
        function () {
            //
        },
    ]);

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(4);
});

it('should call run on endpoint success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new MarketEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);

    $endpoint->run();

    expect($engine->wasHandled())->toBeTrue();
});

it('should call handle on engine when run is invoked', function () {
    $engine = $this->createMock(\Farzai\Bitkub\Contracts\WebSocketEngineInterface::class);
    $engine->expects($this->once())->method('handle');

    $client = new WebSocketClient($engine);

    $client->run();
});

it('should create new instance of market endpoint', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new MarketEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);
});

it('can put stream name as string success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new MarketEndpoint($client);

    $endpoint->listen('market.trade.thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(1);

    $endpoint->listen('trade.thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(2);
});

it('can put stream name with array success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new MarketEndpoint($client);

    $endpoint->listen([
        'market.trade.thb_btc',
        'trade.thb_eth',
    ], function () {
        //
    });

    expect($client->getListeners())->toHaveCount(2);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(1);
    expect($client->getListeners()['market.trade.thb_eth'])->toHaveCount(1);

    $endpoint->listen([
        'market.trade.thb_btc',
        'trade.thb_eth',
    ], function () {
        //
    });

    expect($client->getListeners())->toHaveCount(2);
    expect($client->getListeners()['market.trade.thb_btc'])->toHaveCount(2);
    expect($client->getListeners()['market.trade.thb_eth'])->toHaveCount(2);
});

it('should create live order book endpoint success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new LiveOrderBookEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);
});

it('can put symbol by id success', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen(1, function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['orderbook/1'])->toHaveCount(1);

    $endpoint->listen(1, function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['orderbook/1'])->toHaveCount(2);
});

it('can put symbol by name success', function () {
    $symbolResponseBody = [
        'error' => 0,
        'result' => [
            [
                'id' => 1,
                'symbol' => 'THB_BTC',
                'info' => 'Thai Baht to Bitcoin',
            ],
            [
                'id' => 2,
                'symbol' => 'THB_ETH',
                'info' => 'Thai Baht to Ethereum',
            ],
        ],
    ];

    $httpClient = MockHttpClient::make()
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)))
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)))
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)));

    $baseClient = ClientBuilder::create()
        ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
        ->setHttpClient($httpClient)
        ->build();

    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine, $baseClient);

    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen('thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['orderbook/1'])->toHaveCount(1);

    $endpoint->listen('thb_btc', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(1);
    expect($client->getListeners()['orderbook/1'])->toHaveCount(2);

    $endpoint->listen('thb_eth', function () {
        //
    });

    expect($client->getListeners())->toHaveCount(2);
    expect($client->getListeners()['orderbook/1'])->toHaveCount(2);
    expect($client->getListeners()['orderbook/2'])->toHaveCount(1);
});

it('should throw error if invalid symbol name', function () {
    $symbolResponseBody = [
        'error' => 0,
        'result' => [
            [
                'id' => 1,
                'symbol' => 'THB_BTC',
                'info' => 'Thai Baht to Bitcoin',
            ],
            [
                'id' => 2,
                'symbol' => 'THB_ETH',
                'info' => 'Thai Baht to Ethereum',
            ],
        ],
    ];

    $httpClient = MockHttpClient::make()
        ->addSequence(MockHttpClient::response(200, json_encode($symbolResponseBody)));

    $baseClient = ClientBuilder::create()
        ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
        ->setHttpClient($httpClient)
        ->build();

    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine, $baseClient);

    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen('thb_xxx', function () {
        //
    });
})->throws(\InvalidArgumentException::class, 'Invalid symbol name. Given: thb_xxx');

it('returns same market endpoint instance (lazy singleton)', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $first = $client->market();
    $second = $client->market();

    expect($first)->toBe($second);
});

it('returns same liveOrderBook endpoint instance (lazy singleton)', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $first = $client->liveOrderBook();
    $second = $client->liveOrderBook();

    expect($first)->toBe($second);
});

it('addListener returns static for chaining', function () {
    $engine = new MockWebSocketEngine;
    $client = new WebSocketClient($engine);

    $result = $client->addListener('test', function () {});

    expect($result)->toBe($client);
});
