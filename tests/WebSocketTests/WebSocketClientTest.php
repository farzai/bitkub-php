<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\WebSocket\Endpoints\AbstractEndpoint;
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

it('should create new instance of market endpoint', function () {
    $client = new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    );

    $endpoint = new MarketEndpoint($client);

    expect($endpoint)->toBeInstanceOf(AbstractEndpoint::class);
});
