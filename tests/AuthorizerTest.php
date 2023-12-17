<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Bitkub\Utility;

it('can generate signature success', function () {
    $secret = 'secret';
    $timestamp = 1630483200000;
    $method = 'POST';
    $path = '/api/v3/market/balances';
    $query = '';
    $payload = '';

    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, (string) $timestamp));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $timestamp = (int) Utility::getServerTimestamp($client)->format('U');

    expect($timestamp)->toBe(1630483200000);

    $signature = Utility::generateSignature($secret, $timestamp, $method, $path, $query, $payload);

    expect($signature)->toBe('ae6fd3dc7d85ebea023e54292fa6eebaeea6dc02002433c51b57136eeb0a03e5');
});
