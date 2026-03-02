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
    $result = $request->method('POST');

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request path', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->path('/api/market/orders');

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request options', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->options(['query' => ['symbol' => 'BTC']]);

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can add request interceptor', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $mockRequest = $this->createMock(\Psr\Http\Message\RequestInterface::class);

    $pending = new PendingRequest($client, 'GET', '/api/market/balances');

    $interceptor = $this->createMock(\Farzai\Bitkub\Contracts\RequestInterceptor::class);
    $interceptor->method('apply')->willReturn($mockRequest);

    $result = $pending->withInterceptor($interceptor);

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request body', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'POST', '/api/market/orders');
    $result = $request->withBody(['symbol' => 'BTC', 'quantity' => 1]);

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request query', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->withQuery(['symbol' => 'BTC']);

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request headers', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->withHeaders(['Authorization' => 'Bearer token']);

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request header', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->withHeader('Content-Type', 'application/json');

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request to accept JSON', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'GET', '/api/market/balances');
    $result = $request->acceptJson();

    expect($result)->toBeInstanceOf(PendingRequest::class);
});

it('can set request to send JSON', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $request = new PendingRequest($client, 'POST', '/api/market/orders');
    $result = $request->asJson();

    expect($result)->toBeInstanceOf(PendingRequest::class);
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

it('can createRequest with string body', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $pending = new PendingRequest($client, 'POST', '/api/market/orders');

    $request = $pending->createRequest('POST', '/api/market/orders', [
        'body' => 'raw-string-body',
    ]);

    expect($request)->toBeInstanceOf(\Psr\Http\Message\RequestInterface::class);
    expect($request->getBody()->getContents())->toBe('raw-string-body');
});

it('can createRequest without optional parameters', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $pending = new PendingRequest($client, 'GET', '/api/market/ticker');

    $request = $pending->createRequest('GET', '/api/market/ticker');

    expect($request)->toBeInstanceOf(\Psr\Http\Message\RequestInterface::class);
    expect($request->getMethod())->toBe('GET');
    expect($request->getUri()->getPath())->toBe('/api/market/ticker');
    expect($request->getUri()->getQuery())->toBe('');
});

it('normalizes path with leading slash', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $pending = new PendingRequest($client, 'GET', 'api/market/ticker');

    $request = $pending->createRequest('GET', 'api/market/ticker');

    expect($request->getUri()->getPath())->toBe('/api/market/ticker');
});

it('can createResponse wrapping PSR response', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $pending = new PendingRequest($client, 'GET', '/api/market/ticker');

    $psrRequest = $this->createMock(\Psr\Http\Message\RequestInterface::class);
    $psrResponse = \Farzai\Bitkub\Tests\MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = $pending->createResponse($psrRequest, $psrResponse);

    expect($response)->toBeInstanceOf(\Farzai\Transport\Contracts\ResponseInterface::class);
    expect($response)->toBeInstanceOf(\Farzai\Bitkub\Responses\ResponseWithValidateErrorCode::class);
    expect($response->statusCode())->toBe(200);
});
