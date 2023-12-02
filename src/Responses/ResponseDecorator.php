<?php

namespace Farzai\Bitkub\Responses;

use Farzai\Transport\Contracts\ResponseInterface;
use Farzai\Transport\Traits\PsrResponseTrait;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;

abstract class ResponseDecorator implements ResponseInterface
{
    use PsrResponseTrait;

    protected ResponseInterface $response;

    /**
     * Create a new response instance.
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Return the response status code.
     */
    public function statusCode(): int
    {
        return $this->response->statusCode();
    }

    /**
     * Return the response body.
     */
    public function body(): string
    {
        return $this->response->body();
    }

    /**
     * Return the response headers.
     */
    public function headers(): array
    {
        return $this->response->headers();
    }

    /**
     * Check if the response is successfull.
     */
    public function isSuccessfull(): bool
    {
        return $this->response->isSuccessfull();
    }

    /**
     * Return the json decoded response.
     */
    public function json(string $key = null): mixed
    {
        return $this->response->json($key);
    }

    /**
     * Throw an exception if the response is not successfull.
     *
     * @param  callable<\Farzai\Transport\Contracts\ResponseInterface>  $callback Custom callback to throw an exception.
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function throw(callable $callback = null)
    {
        return $this->response->throw($callback);
    }

    /**
     * Return the psr request.
     */
    public function getPsrRequest(): PsrRequestInterface
    {
        return $this->response->getPsrRequest();
    }
}
