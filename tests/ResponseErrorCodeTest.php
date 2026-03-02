<?php

declare(strict_types=1);

use Farzai\Bitkub\Constants\ErrorCodes;
use Farzai\Bitkub\Exceptions\BitkubResponseErrorCodeException;
use Farzai\Bitkub\Responses\ResponseWithValidateErrorCode;
use Farzai\Bitkub\Tests\MockHttpClient;
use Farzai\Transport\Response;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

it('decorator must be instance of ResponseInterface', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
    ]), [
        'Content-Type' => 'application/json',
    ]);

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response)->toBeInstanceOf(\Farzai\Transport\Contracts\ResponseInterface::class);

    expect($response->statusCode())->toBe(200);
    expect($response->body())->toBe(json_encode([
        'error' => 0,
    ]));
    expect($response->headers())->toBe([
        'Content-Type' => 'application/json',
    ]);
    expect($response->isSuccessful())->toBeTrue();
    expect($response->json('error'))->toBe(0);
    expect($response->getPsrRequest())->toBe($psrRequest);
});

it('should throw exception when error code is not 0', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 1,
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $response->throw();

})->throws(BitkubResponseErrorCodeException::class, 'Invalid JSON payload');

it('should not throw exception when error code is 0', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $response->throw();

    expect($response->json('error'))->toBe(0);
});

it('should valid response if response without farzai response', function () {
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => ErrorCodes::MISSING_TIMESTAMP,
    ]));

    $exception = new BitkubResponseErrorCodeException($psrResponse);

    expect($exception->getCode())->toBe(ErrorCodes::MISSING_TIMESTAMP);
    expect($exception->getMessage())->toBe('Missing timestamp');
    expect($exception->getResponse())->toBe($psrResponse);
});

it('should handle missing error code gracefully', function () {
    $psrResponse = MockHttpClient::response(200, json_encode([
        // Empty error code
    ]));

    $exception = new BitkubResponseErrorCodeException($psrResponse);

    expect($exception->getMessage())->toBe('Malformed response: error code not found.');
    expect($exception->getCode())->toBe(0);
});

it('should not throw exception when error code is null', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'result' => 'ok',
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $result = $response->throw();

    expect($result)->toBeInstanceOf(ResponseWithValidateErrorCode::class);
});

it('throw returns static for chaining', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $result = $response->throw();

    expect($result)->toBe($response);
});

it('decorator delegates jsonOrNull method', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
        'result' => ['balance' => 100],
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->jsonOrNull('result'))->toBe(['balance' => 100]);
    expect($response->jsonOrNull('nonexistent'))->toBeNull();
});

it('decorator delegates toArray method', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
        'result' => 'ok',
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->toArray())->toBe(['error' => 0, 'result' => 'ok']);
});

it('decorator delegates PSR-7 getStatusCode', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(201, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getStatusCode())->toBe(201);
});

it('throw with custom callback still validates error codes', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 1,
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $callbackCalled = false;
    $response->throw(function () use (&$callbackCalled) {
        $callbackCalled = true;
    });
})->throws(BitkubResponseErrorCodeException::class, 'Invalid JSON payload');

it('throw with custom callback is called when no error', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 0,
    ]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $callbackCalled = false;
    $response->throw(function () use (&$callbackCalled) {
        $callbackCalled = true;
    });

    expect($callbackCalled)->toBeTrue();
});

it('decorator delegates PSR-7 getBody', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getBody())->toBeInstanceOf(\Psr\Http\Message\StreamInterface::class);
});

it('decorator delegates getReasonPhrase', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getReasonPhrase())->toBeString();
});

it('decorator delegates getProtocolVersion', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getProtocolVersion())->toBeString();
});

it('decorator delegates getHeaders via PSR-7', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]), [
        'Content-Type' => 'application/json',
    ]);

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getHeaders())->toBeArray();
});

it('decorator delegates hasHeader', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    // The mock may or may not have headers, but hasHeader should return a bool
    expect($response->hasHeader('Content-Type'))->toBeBool();
});

it('decorator delegates getHeader', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getHeader('Content-Type'))->toBeArray();
});

it('decorator delegates getHeaderLine', function () {
    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = MockHttpClient::response(200, json_encode(['error' => 0]));

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    expect($response->getHeaderLine('Content-Type'))->toBeString();
});

it('exception with unknown error code uses fallback message', function () {
    $psrResponse = MockHttpClient::response(200, json_encode([
        'error' => 12345,
    ]));

    $exception = new BitkubResponseErrorCodeException($psrResponse);

    expect($exception->getCode())->toBe(12345);
    expect($exception->getMessage())->toBe('Unknown error code: 12345');
});
