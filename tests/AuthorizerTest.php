<?php

declare(strict_types=1);

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\SystemEndpoint;
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

    $endpoint = new SystemEndpoint($client);

    $timestamp = (int) $endpoint->serverTimestamp()->throw()->body();

    expect($timestamp)->toBe(1630483200000);

    $signature = Utility::generateSignature($secret, $timestamp, $method, $path, $query, $payload);

    expect($signature)->toBe('ae6fd3dc7d85ebea023e54292fa6eebaeea6dc02002433c51b57136eeb0a03e5');
});

it('generates different signatures with non-empty query', function () {
    $secret = 'secret';
    $timestamp = 1630483200000;
    $method = 'GET';
    $path = '/api/market/trades';

    $sigWithoutQuery = Utility::generateSignature($secret, $timestamp, $method, $path, '', '');
    $sigWithQuery = Utility::generateSignature($secret, $timestamp, $method, $path, '?sym=THB_BTC', '');

    expect($sigWithoutQuery)->not->toBe($sigWithQuery);
});

it('generates different signatures with non-empty payload', function () {
    $secret = 'secret';
    $timestamp = 1630483200000;
    $method = 'POST';
    $path = '/api/v3/market/place-bid';

    $sigWithoutPayload = Utility::generateSignature($secret, $timestamp, $method, $path, '', '');
    $sigWithPayload = Utility::generateSignature($secret, $timestamp, $method, $path, '', '{"sym":"THB_BTC","amt":1000}');

    expect($sigWithoutPayload)->not->toBe($sigWithPayload);
});

it('generates different signatures for different methods', function () {
    $secret = 'secret';
    $timestamp = 1630483200000;
    $path = '/api/market/trades';

    $sigGet = Utility::generateSignature($secret, $timestamp, 'GET', $path, '', '');
    $sigPost = Utility::generateSignature($secret, $timestamp, 'POST', $path, '', '');

    expect($sigGet)->not->toBe($sigPost);
});

it('generates consistent signatures for same inputs', function () {
    $secret = 'secret';
    $timestamp = 1630483200000;
    $method = 'POST';
    $path = '/api/v3/market/balances';
    $query = '?sym=THB_BTC';
    $payload = '{"amt":1000}';

    $sig1 = Utility::generateSignature($secret, $timestamp, $method, $path, $query, $payload);
    $sig2 = Utility::generateSignature($secret, $timestamp, $method, $path, $query, $payload);

    expect($sig1)->toBe($sig2);
    expect(strlen($sig1))->toBe(64); // SHA-256 hex
});
