<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\MarketEndpoint;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Transport\Contracts\ResponseInterface;

it('should create market endpoint success', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $market = $client->market();

    expect($market)->toBeInstanceOf(MarketEndpoint::class);
});

it('should get balance success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'THB' => [
                    'available' => 1000,
                    'reserved' => 0,
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->balances();

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.THB.available'))->toBe(1000);
});

it('should get symbols success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'THB_BTC' => [
                    'id' => 1,
                    'last' => 1000,
                    'lowestAsk' => 1000,
                    'highestBid' => 1000,
                    'percentChange' => 0,
                    'baseVolume' => 0,
                    'quoteVolume' => 0,
                    'isFrozen' => 0,
                    'high24hr' => 0,
                    'low24hr' => 0,
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->symbols();

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.THB_BTC.id'))->toBe(1);
});

it('should get ticker success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'THB_BTC' => [
                    'id' => 1,
                    'last' => 1000,
                    'lowestAsk' => 1000,
                    'highestBid' => 1000,
                    'percentChange' => 0,
                    'baseVolume' => 0,
                    'quoteVolume' => 0,
                    'isFrozen' => 0,
                    'high24hr' => 0,
                    'low24hr' => 0,
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->ticker();

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.THB_BTC.id'))->toBe(1);
});

it('should get trades success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                [
                    1529516287,
                    10000.00,
                    0.09975000,
                    'BUY',
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->trades([
        'sym' => 'THB_BTC',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.0.0'))->toBe(1529516287);
    expect($response->json('result.0.2'))->toBe(0.09975000);
});

it('should get bids success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                [
                    '1', // order id
                    1529453033, // timestamp
                    997.50, // volume
                    10000.00, // rate
                    0.09975000, // amount
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->bids([
        'sym' => 'THB_BTC',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.0.0'))->toBe('1');
    expect($response->json('result.0.2'))->toBe(997.50);
});

it('should get asks success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                [
                    '1', // order id
                    1529453033, // timestamp
                    997.50, // volume
                    10000.00, // rate
                    0.09975000, // amount
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->asks([
        'sym' => 'THB_BTC',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.0.0'))->toBe('1');
    expect($response->json('result.0.2'))->toBe(997.50);
});

it('should get books success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'bids' => [
                    [
                        '1', // order id
                        1529453033, // timestamp
                        997.50, // volume
                        10000.00, // rate
                        0.09975000, // amount
                    ],
                ],
                'asks' => [
                    [
                        '1', // order id
                        1529453033, // timestamp
                        997.50, // volume
                        10000.00, // rate
                        0.09975000, // amount
                    ],
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->books([
        'sym' => 'THB_BTC',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.bids.0.0'))->toBe('1');
    expect($response->json('result.bids.0.2'))->toBe(997.50);
    expect($response->json('result.asks.0.0'))->toBe('1');
    expect($response->json('result.asks.0.2'))->toBe(997.50);
});

it('should get wallet success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'THB' => [
                    'available' => 1000,
                    'reserved' => 0,
                ],
                'BTC' => [
                    'available' => 0.09975000,
                    'reserved' => 0,
                ],
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->wallet();

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.THB.available'))->toBe(1000);
    expect($response->json('result.BTC.available'))->toBe(0.09975000);
});

it('should call placeBid success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'id' => '1',
                'hash' => 'fwQ6dnQWQPs4cbatF5Am2xCDP1J',
                'typ' => 'limit',
                'amt' => 1000,
                'rat' => 10000,
                'fee' => 2.5,
                'cre' => 2.5,
                'rec' => 0.06666666,
                'ts' => 1533834547,
                'ci' => 'input_client_id',
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->placeBid([
        'sym' => 'THB_BTC',
        'amt' => 1000,
        'rat' => 10000,
        'typ' => 'limit',
        'client_id' => 'xxxx',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.id'))->toBe('1');
    expect($response->json('result.hash'))->toBe('fwQ6dnQWQPs4cbatF5Am2xCDP1J');
});

it('should call placeAsk success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
            'result' => [
                'id' => '1',
                'hash' => 'fwQ6dnQWQPs4cbatF5Am2xCDP1J',
                'typ' => 'limit',
                'amt' => 1000,
                'rat' => 10000,
                'fee' => 2.5,
                'cre' => 2.5,
                'rec' => 0.06666666,
                'ts' => 1533834547,
                'ci' => 'input_client_id',
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->placeAsk([
        'sym' => 'THB_BTC',
        'amt' => 1000,
        'rat' => 10000,
        'typ' => 'limit',
        'client_id' => 'xxxx',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result.id'))->toBe('1');
    expect($response->json('result.hash'))->toBe('fwQ6dnQWQPs4cbatF5Am2xCDP1J');
});

it('should call cancelOrder success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            'error' => 0,
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $market = $client->market();

    $response = $market->cancelOrder([
        'sym' => 'THB_BTC',
        'id' => '1',
        'hash' => 'fwQ6dnQWQPs4cbatF5Am2xCDP1J',
        'sd' => 'buy',
    ]);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('error'))->toBe(0);
});
