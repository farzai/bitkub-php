<?php

declare(strict_types=1);

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\MarketEndpoint;

it('filterParams strips null values', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $endpoint = new MarketEndpoint($client);

    // Use reflection to test the protected filterParams method
    $reflection = new ReflectionMethod($endpoint, 'filterParams');

    $result = $reflection->invoke($endpoint, [
        'sym' => 'THB_BTC',
        'lmt' => null,
        'page' => 1,
    ]);

    expect($result)->toBe([
        'sym' => 'THB_BTC',
        'page' => 1,
    ]);
});

it('filterParams strips empty string values', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $endpoint = new MarketEndpoint($client);

    $reflection = new ReflectionMethod($endpoint, 'filterParams');

    $result = $reflection->invoke($endpoint, [
        'sym' => 'THB_BTC',
        'lmt' => '',
        'page' => 0,
    ]);

    expect($result)->toBe([
        'sym' => 'THB_BTC',
        'page' => 0,
    ]);
});

it('filterParams keeps zero and false values', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $endpoint = new MarketEndpoint($client);

    $reflection = new ReflectionMethod($endpoint, 'filterParams');

    $result = $reflection->invoke($endpoint, [
        'sym' => 'THB_BTC',
        'lmt' => 0,
        'active' => false,
        'empty' => null,
        'blank' => '',
    ]);

    expect($result)->toHaveKey('sym');
    expect($result)->toHaveKey('lmt');
    expect($result)->toHaveKey('active');
    expect($result)->not->toHaveKey('empty');
    expect($result)->not->toHaveKey('blank');
});

it('filterParams returns empty array from all-null input', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $endpoint = new MarketEndpoint($client);

    $reflection = new ReflectionMethod($endpoint, 'filterParams');

    $result = $reflection->invoke($endpoint, [
        'a' => null,
        'b' => '',
    ]);

    expect($result)->toBe([]);
});
