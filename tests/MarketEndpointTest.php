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
    expect($response->json('result'))->toBe([
        'THB' => [
            'available' => 1000,
            'reserved' => 0,
        ],
    ]);
});
