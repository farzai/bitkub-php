<?php

namespace Farzai\Bitkub\Responses;

use Farzai\Transport\Contracts\ResponseInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractResponseDecorator implements ResponseInterface
{
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
     * Check if the response is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->response->isSuccessful();
    }

    /**
     * Return the json decoded response.
     */
    public function json(?string $key = null): mixed
    {
        return $this->response->json($key);
    }

    /**
     * Return the json decoded response or null.
     */
    public function jsonOrNull(?string $key = null): mixed
    {
        return $this->response->jsonOrNull($key);
    }

    /**
     * Return the response as an array.
     */
    public function toArray(): array
    {
        return $this->response->toArray();
    }

    /**
     * Throw an exception if the response is not successful.
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function throw(?callable $callback = null): static
    {
        $this->response->throw($callback);

        return $this;
    }

    /**
     * Return the psr request.
     */
    public function getPsrRequest(): PsrRequestInterface
    {
        return $this->response->getPsrRequest();
    }

    // PSR-7 ResponseInterface delegation

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return $this->cloneWithResponse($this->response->withStatus($code, $reasonPhrase));
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): static
    {
        return $this->cloneWithResponse($this->response->withProtocolVersion($version));
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): static
    {
        return $this->cloneWithResponse($this->response->withHeader($name, $value));
    }

    public function withAddedHeader(string $name, $value): static
    {
        return $this->cloneWithResponse($this->response->withAddedHeader($name, $value));
    }

    public function withoutHeader(string $name): static
    {
        return $this->cloneWithResponse($this->response->withoutHeader($name));
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body): static
    {
        return $this->cloneWithResponse($this->response->withBody($body));
    }

    private function cloneWithResponse(ResponseInterface $response): static
    {
        $clone = clone $this;
        $clone->response = $response;

        return $clone;
    }
}
