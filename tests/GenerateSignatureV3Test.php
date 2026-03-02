<?php

declare(strict_types=1);

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Bitkub\Tests\MockHttpClient;

it('applies signature headers to request', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp());

    $client = ClientBuilder::create()
        ->setCredentials('my-api-key', 'my-secret')
        ->setHttpClient($psrClient)
        ->build();

    $config = $client->getConfig();
    $interceptor = new GenerateSignatureV3($config, $client);

    // Build a simple request
    $request = (new \Farzai\Transport\RequestBuilder)
        ->method('POST')
        ->uri('/api/v3/market/balances')
        ->build();

    $signedRequest = $interceptor->apply($request);

    expect($signedRequest->getHeaderLine('X-BTK-APIKEY'))->toBe('my-api-key');
    expect($signedRequest->getHeaderLine('X-BTK-SIGN'))->not->toBeEmpty();
    expect($signedRequest->getHeaderLine('X-BTK-TIMESTAMP'))->not->toBeEmpty();
});

it('applies signature with query string present', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp());

    $client = ClientBuilder::create()
        ->setCredentials('my-api-key', 'my-secret')
        ->setHttpClient($psrClient)
        ->build();

    $config = $client->getConfig();
    $interceptor = new GenerateSignatureV3($config, $client);

    // Build a request with query params
    $request = (new \Farzai\Transport\RequestBuilder)
        ->method('GET')
        ->uri('/api/v3/market/my-open-orders')
        ->withQuery(['sym' => 'THB_BTC'])
        ->build();

    $signedRequest = $interceptor->apply($request);

    expect($signedRequest->getHeaderLine('X-BTK-APIKEY'))->toBe('my-api-key');
    expect($signedRequest->getHeaderLine('X-BTK-SIGN'))->not->toBeEmpty();
    // Signature should differ from a request without query
    $signedRequest2 = $interceptor->apply(
        (new \Farzai\Transport\RequestBuilder)
            ->method('GET')
            ->uri('/api/v3/market/my-open-orders')
            ->build()
    );

    expect($signedRequest->getHeaderLine('X-BTK-SIGN'))
        ->not->toBe($signedRequest2->getHeaderLine('X-BTK-SIGN'));
});

it('applies signature with request body', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp());

    $client = ClientBuilder::create()
        ->setCredentials('my-api-key', 'my-secret')
        ->setHttpClient($psrClient)
        ->build();

    $config = $client->getConfig();
    $interceptor = new GenerateSignatureV3($config, $client);

    // Build a request with a body
    $request = (new \Farzai\Transport\RequestBuilder)
        ->method('POST')
        ->uri('/api/v3/market/place-bid')
        ->withJson(['sym' => 'THB_BTC', 'amt' => 1000, 'rat' => 15000, 'typ' => 'limit'])
        ->build();

    $signedRequest = $interceptor->apply($request);

    expect($signedRequest->getHeaderLine('X-BTK-APIKEY'))->toBe('my-api-key');
    expect($signedRequest->getHeaderLine('X-BTK-SIGN'))->not->toBeEmpty();
    expect($signedRequest->getHeaderLine('X-BTK-TIMESTAMP'))->not->toBeEmpty();
});

it('reuses timestamp within sync interval', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => MockHttpClient::responseServerTimestamp());

    $client = ClientBuilder::create()
        ->setCredentials('my-api-key', 'my-secret')
        ->setHttpClient($psrClient)
        ->build();

    $config = $client->getConfig();
    $interceptor = new GenerateSignatureV3($config, $client);

    $request = (new \Farzai\Transport\RequestBuilder)
        ->method('POST')
        ->uri('/api/v3/market/balances')
        ->build();

    // First call syncs with server
    $signed1 = $interceptor->apply($request);

    // Second call should reuse the cached drift (no second HTTP call needed)
    $signed2 = $interceptor->apply($request);

    expect($signed1->getHeaderLine('X-BTK-APIKEY'))->toBe('my-api-key');
    expect($signed2->getHeaderLine('X-BTK-APIKEY'))->toBe('my-api-key');
});
