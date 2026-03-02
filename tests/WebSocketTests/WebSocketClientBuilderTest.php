<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Tests\WebSocketTests\MockWebSocketEngine;
use Farzai\Bitkub\WebSocketClient;
use Farzai\Bitkub\WebSocketClientBuilder;

it('can create WebSocketClient via builder', function () {
    $baseClient = ClientBuilder::create()
        ->setCredentials('key', 'secret')
        ->build();

    $ws = WebSocketClientBuilder::create()
        ->setClient($baseClient)
        ->build();

    expect($ws)->toBeInstanceOf(WebSocketClient::class);
    expect($ws->getClient())->toBe($baseClient);
});

it('uses NullLogger when no logger set', function () {
    $ws = WebSocketClientBuilder::create()
        ->build();

    expect($ws->getLogger())->toBeInstanceOf(\Psr\Log\NullLogger::class);
});

it('can set custom base URL', function () {
    $ws = WebSocketClientBuilder::create()
        ->setBaseUrl('wss://custom.example.com/ws/')
        ->build();

    expect($ws)->toBeInstanceOf(WebSocketClient::class);
});

it('can set custom engine for testing', function () {
    $engine = new MockWebSocketEngine;

    $ws = WebSocketClientBuilder::create()
        ->setEngine($engine)
        ->build();

    expect($ws)->toBeInstanceOf(WebSocketClient::class);

    $ws->run();

    expect($engine->wasHandled())->toBeTrue();
});

it('throws when reconnect attempts is negative', function () {
    WebSocketClientBuilder::create()
        ->setReconnectAttempts(-1);
})->throws(\InvalidArgumentException::class, 'Reconnect attempts must be greater than or equal to 0.');

it('throws when reconnect delay is negative', function () {
    WebSocketClientBuilder::create()
        ->setReconnectDelayMs(-1);
})->throws(\InvalidArgumentException::class, 'Reconnect delay must be greater than or equal to 0.');

it('can set custom logger', function () {
    $logger = new \Psr\Log\NullLogger;

    $ws = WebSocketClientBuilder::create()
        ->setLogger($logger)
        ->build();

    expect($ws->getLogger())->toBe($logger);
});

it('uses client logger when no explicit logger set', function () {
    $baseClient = ClientBuilder::create()
        ->setCredentials('key', 'secret')
        ->build();

    $ws = WebSocketClientBuilder::create()
        ->setClient($baseClient)
        ->build();

    expect($ws->getLogger())->toBe($baseClient->getLogger());
});
