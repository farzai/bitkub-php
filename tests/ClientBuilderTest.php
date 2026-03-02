<?php

use Farzai\Bitkub\Client;
use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Exceptions\InvalidArgumentException;

it('can create client instance without throwing exception', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    expect($client)->toBeInstanceOf(Client::class);
});

it('should throw exception when call method without api key', function () {
    $client = ClientBuilder::create()
        ->build();

    $client->market()->balances();
})->throws(InvalidArgumentException::class, 'API key is required');

it('should throw exception when call method without secret key', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', '')
        ->build();

    $client->market()->balances();
})->throws(InvalidArgumentException::class, 'Secret key is required');

it('can set custom http client', function () {
    $httpClient = $this->createMock(\Psr\Http\Client\ClientInterface::class);

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($httpClient)
        ->build();

    expect($client)->toBeInstanceOf(Client::class);
});

it('can set custom logger', function () {
    $logger = $this->createMock(\Psr\Log\LoggerInterface::class);

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setLogger($logger)
        ->build();

    expect($client)->toBeInstanceOf(Client::class);
});

it('can set retries', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setRetries(5)
        ->build();

    expect($client)->toBeInstanceOf(Client::class);
});

it('should throw exception when retries is negative', function () {
    ClientBuilder::create()
        ->setRetries(-1);
})->throws(\InvalidArgumentException::class, 'Retries must be greater than or equal to 0.');
