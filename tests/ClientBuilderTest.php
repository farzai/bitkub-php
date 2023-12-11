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
