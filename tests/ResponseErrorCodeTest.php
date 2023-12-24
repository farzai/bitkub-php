<?php

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
    expect($response->isSuccessfull())->toBeTrue();
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

it('should be error code not found', function () {
    $psrResponse = MockHttpClient::response(200, json_encode([
        // Empty error code
    ]));

    new BitkubResponseErrorCodeException($psrResponse);
})->throws(\Exception::class, 'Error code not found.');
