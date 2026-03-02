<?php

declare(strict_types=1);

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

it('can set retries to zero', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setRetries(0)
        ->build();

    expect($client)->toBeInstanceOf(Client::class);
});

it('returns same market endpoint instance (lazy singleton)', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $first = $client->market();
    $second = $client->market();

    expect($first)->toBe($second);
});

it('returns same crypto endpoint instance (lazy singleton)', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $first = $client->crypto();
    $second = $client->crypto();

    expect($first)->toBe($second);
});

it('returns same user endpoint instance (lazy singleton)', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $first = $client->user();
    $second = $client->user();

    expect($first)->toBe($second);
});

it('returns same system endpoint instance (lazy singleton)', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $first = $client->system();
    $second = $client->system();

    expect($first)->toBe($second);
});

it('can access getTransport', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    expect($client->getTransport())->toBeInstanceOf(\Farzai\Transport\Transport::class);
});

it('can access getConfig with correct keys', function () {
    $client = ClientBuilder::create()
        ->setCredentials('my-api-key', 'my-secret')
        ->build();

    $config = $client->getConfig();

    expect($config)->toHaveKey('api_key', 'my-api-key');
    expect($config)->toHaveKey('secret', 'my-secret');
});

it('can access getLogger', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    expect($client->getLogger())->toBeInstanceOf(\Psr\Log\LoggerInterface::class);
});

it('uses custom logger when set', function () {
    $logger = new \Psr\Log\NullLogger;

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setLogger($logger)
        ->build();

    expect($client->getLogger())->toBe($logger);
});
