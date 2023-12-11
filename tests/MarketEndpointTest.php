<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\MarketEndpoint;
use Farzai\Transport\Contracts\ResponseInterface;

it('should create market endpoint success', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $market = $client->market();

    expect($market)->toBeInstanceOf(MarketEndpoint::class);
});

it('should get balance success', function () {
    $httpClient = $this->createMock(\Psr\Http\Client\ClientInterface::class);

    $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
    $stream->method('getContents')->willReturn(json_encode([
        'error' => 0,
        'result' => [
            'THB' => [
                'available' => 1000,
                'reserved' => 0,
            ],
        ],
    ]));

    $psrResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
    $psrResponse->method('getStatusCode')->willReturn(200);
    $psrResponse->method('getBody')->willReturn($stream);

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($httpClient)
        ->build();

    $market = $client->market();

    $httpClient->expects($this->once())
        ->method('sendRequest')
        ->willReturn($psrResponse);

    $response = $market->balances();

    expect($response)->toBeInstanceOf(ResponseInterface::class);
    expect($response->json('result'))->toBe([
        'THB' => [
            'available' => 1000,
            'reserved' => 0,
        ],
    ]);
});
