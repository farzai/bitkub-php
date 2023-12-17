<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Requests\PendingRequest;

it('can create pending request instance', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');

    expect($request)->toBeInstanceOf(PendingRequest::class);
});

it('can set request method', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->method('POST');

    expect($request->method)->toBe('POST');
});

it('can set request path', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->path('/api/market/orders');

    expect($request->path)->toBe('/api/market/orders');
});

it('can set request options', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->options(['query' => ['symbol' => 'BTC']]);

    expect($request->options)->toBe(['query' => ['symbol' => 'BTC']]);
});

it('can add request interceptor', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = $this->createMock(\Psr\Http\Message\RequestInterface::class);

    $pending = new PendingRequest($client, 'GET', '/api/market/balances');

    $interceptor = $this->createMock(\Farzai\Bitkub\Contracts\RequestInterceptor::class);
    $interceptor->method('apply')->willReturn($request);

    $pending->withInterceptor($interceptor);

    expect($pending->interceptors)->toContain($interceptor);
});

it('can set request body', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'POST', '/api/market/orders');
    $request->withBody(['symbol' => 'BTC', 'quantity' => 1]);

    expect($request->options['body'])->toBe(['symbol' => 'BTC', 'quantity' => 1]);
});

it('can set request query', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->withQuery(['symbol' => 'BTC']);

    expect($request->options['query'])->toBe(['symbol' => 'BTC']);
});

it('can set request headers', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->withHeaders(['Authorization' => 'Bearer token']);

    expect($request->options['headers'])->toBe(['Authorization' => 'Bearer token']);
});

it('can set request header', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->withHeader('Content-Type', 'application/json');

    expect($request->options['headers']['Content-Type'])->toBe('application/json');
});

it('can set request to accept JSON', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $request->acceptJson();

    expect($request->options['headers']['Accept'])->toBe('application/json');
});

it('can set request to send JSON', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'POST', '/api/market/orders');
    $request->asJson();

    expect($request->options['headers']['Content-Type'])->toBe('application/json');
});

it('it can createRequest success', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $pending = new PendingRequest($client, 'GET', '/api/market/orders');

    $request = $pending->createRequest('POST', '/api/market/trades', [
        'body' => ['symbol' => 'BTC'],
        'query' => ['symbol' => 'BTC'],
        'headers' => ['Content-Type' => 'application/json'],
    ]);

    expect($request)->toBeInstanceOf(\Psr\Http\Message\RequestInterface::class);
    expect($request->getMethod())->toBe('POST');
    expect($request->getUri()->getPath())->toBe('/api/market/trades');
    expect($request->getBody()->getContents())->toBe(json_encode(['symbol' => 'BTC']));
    expect($request->getUri()->getQuery())->toBe('symbol=BTC');
    expect($request->getHeaderLine('Content-Type'))->toBe('application/json');
});
