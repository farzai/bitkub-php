<?php

namespace Farzai\Bitkub\Requests;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Contracts\RequestInterceptor;
use Farzai\Bitkub\Responses\ResponseWithValidateErrorCode;
use Farzai\Transport\Contracts\ResponseInterface;
use Farzai\Transport\Request;
use Farzai\Transport\Response;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class PendingRequest
{
    public ClientInterface $client;

    public string $method;

    public string $path;

    public array $options;

    /**
     * The request interceptors.
     *
     * @var array<\Farzai\Bitkub\Contracts\RequestInterceptor>
     */
    public $interceptors = [];

    /**
     * Create a new pending request instance.
     */
    public function __construct(ClientInterface $client, string $method, string $path, array $options = [])
    {
        $this->client = $client;
        $this->method = $method;
        $this->path = $path;
        $this->options = $options;
    }

    /**
     * Set the request method.
     */
    public function method(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set the request path.
     */
    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set the request options.
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function withInterceptor(RequestInterceptor $interceptor)
    {
        $this->interceptors[] = $interceptor;

        return $this;
    }

    /**
     * Set the request body.
     */
    public function withBody(mixed $body)
    {
        $this->options['body'] = $body;

        return $this;
    }

    /**
     * Set the request query.
     */
    public function withQuery(array $query)
    {
        $this->options['query'] = $query;

        return $this;
    }

    /**
     * Set the request headers.
     */
    public function withHeaders(array $headers)
    {
        $this->options['headers'] = array_merge($this->options['headers'] ?? [], $headers);

        return $this;
    }

    public function withHeader(string $key, string $value)
    {
        $this->options['headers'][$key] = $value;

        return $this;
    }

    public function acceptJson()
    {
        return $this->withHeader('Accept', 'application/json');
    }

    public function asJson()
    {
        return $this->withHeader('Content-Type', 'application/json');
    }

    /**
     * Send the request.
     */
    public function send(): ResponseInterface
    {
        $request = $this->createRequest($this->method, $this->path, $this->options);

        // Apply interceptors
        foreach ($this->interceptors as $interceptor) {
            $request = $interceptor->apply($request);
        }

        return $this->createResponse($request, $this->client->sendRequest($request));
    }

    /**
     * Create a new request instance.
     */
    public function createRequest(string $method, string $path, array $options = []): PsrRequestInterface
    {
        // Normalize path
        $path = '/'.trim($path, '/');

        // Query
        if (isset($options['query']) && is_array($options['query']) && ! empty($options['query'])) {
            $path .= '?'.http_build_query($options['query']);
        }

        // Set body
        if (isset($options['body'])) {
            $body = $options['body'];

            // Convert array to json
            if (is_array($body)) {
                $body = json_encode($body);
            }
        }

        // Set headers
        $headers = $options['headers'] ?? [];

        return new Request($method, $path, $headers, $body ?? null);
    }

    /**
     * Create a new response instance.
     */
    public function createResponse(PsrRequestInterface $request, PsrResponseInterface $baseResponse): ResponseInterface
    {
        $response = new Response($request, $baseResponse);
        $response = new ResponseWithValidateErrorCode($response);

        return $response;
    }
}
