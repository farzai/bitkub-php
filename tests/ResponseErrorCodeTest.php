<?php

use Farzai\Bitkub\Exceptions\BitkubResponseErrorCodeException;
use Farzai\Bitkub\Responses\ResponseWithValidateErrorCode;
use Farzai\Transport\Response;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

it('decorator must be instance of ResponseInterface', function () {
    $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
    $stream->method('getContents')->willReturn(json_encode([
        'error' => 0,
    ]));

    $psrRequest = $this->createMock(PsrRequestInterface::class);
    $psrResponse = $this->createMock(PsrResponseInterface::class);
    $psrResponse->method('getStatusCode')->willReturn(200);
    $psrResponse->method('getBody')->willReturn($stream);
    $psrResponse->method('getHeaders')->willReturn([
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
    $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
    $stream->method('getContents')->willReturn(json_encode([
        'error' => 1,
    ]));

    $psrResponse = $this->createMock(PsrResponseInterface::class);
    $psrResponse->method('getStatusCode')->willReturn(200);
    $psrResponse->method('getBody')->willReturn($stream);

    $psrRequest = $this->createMock(PsrRequestInterface::class);

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $response->throw();

})->throws(BitkubResponseErrorCodeException::class, 'Invalid JSON payload');

it('should not throw exception when error code is 0', function () {
    $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
    $stream->method('getContents')->willReturn(json_encode([
        'error' => 0,
    ]));

    $psrResponse = $this->createMock(PsrResponseInterface::class);
    $psrResponse->method('getStatusCode')->willReturn(200);
    $psrResponse->method('getBody')->willReturn($stream);

    $psrRequest = $this->createMock(PsrRequestInterface::class);

    $response = new Response($psrRequest, $psrResponse);
    $response = new ResponseWithValidateErrorCode($response);

    $response->throw();

    expect($response->json('error'))->toBe(0);
});
