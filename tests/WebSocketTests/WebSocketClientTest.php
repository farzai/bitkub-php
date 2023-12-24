<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Bitkub\WebSocket\Endpoints\AbstractEndpoint;
use Farzai\Bitkub\WebSocket\Endpoints\LiveOrderBookEndpoint;
use Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint;
use Farzai\Bitkub\WebSocketClient;

it('should create new instance of WebSocketClient', function () {
    $client = new WebSocketClient(
        $baseClient = ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

    expect($client)->toBeInstanceOf(WebSocketClient::class);

    expect($client->getConfig())->toBe($baseClient->getConfig());
    expect($client->getLogger())->toBe($baseClient->getLogger());
});

it('should add listener success', function () {
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

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
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

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
    $client = $this->createMock(WebSocketClient::class);
    $client->expects($this->once())->method('run');

    $endpoint = new MarketEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);

    $endpoint->run();
});

it('should call handle on client success', function () {
    $engine = $this->createMock(\Farzai\Bitkub\Contracts\WebSocketEngineInterface::class);
    $engine->expects($this->once())->method('handle');

    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build(),
        $engine,
    );

    $client->run();
});

it('should create new instance of market endpoint', function () {
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

    $endpoint = new MarketEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);
});

it('can put stream name as string success', function () {
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

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
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

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
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

    $endpoint = new LiveOrderBookEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);
});

it('can put symbol by id success', function () {
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

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

    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->setHttpClient($httpClient)
            ->build()
    );

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

    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->setHttpClient($httpClient)
            ->build()
    );

    $endpoint = new LiveOrderBookEndpoint($client);

    $endpoint->listen('thb_xxx', function () {
        //
    });
})->throws(\InvalidArgumentException::class, 'Invalid symbol name. Given: thb_xxx');
