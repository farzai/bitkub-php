<?php

use Farzai\Bitkub\Client;
use Farzai\Bitkub\Exceptions\InvalidArgumentException;

it('can create client instance without throwing exception', function () {
    $client = new Client();

    expect($client)->toBeInstanceOf(Client::class);
});

it('should throw exception when call method without api key', function () {
    $client = new Client();

    $client->balances();
})->throws(InvalidArgumentException::class, 'API key is required');
