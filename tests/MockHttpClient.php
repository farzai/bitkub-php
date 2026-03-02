<?php

declare(strict_types=1);

namespace Farzai\Bitkub\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface as PsrStreamInterface;

class MockHttpClient extends TestCase implements PsrClientInterface
{
    /**
     * The sequence of responses.
     *
     * @var array<int, PsrResponseInterface>
     */
    private array $sequence = [];

    /**
     * @var array<int, PsrRequestInterface>
     */
    private array $recordedRequests = [];

    /**
     * Create a new mock http client instance.
     */
    public static function make(): self
    {
        return new static('mock-http-client');
    }

    /**
     * Add a sequence of responses.
     *
     * @param  PsrResponseInterface|callable<PsrResponseInterface>  ...$responses
     */
    public function addSequence(PsrResponseInterface|callable ...$responses): self
    {
        foreach ($responses as $response) {
            if (is_callable($response)) {
                $response = $response($this);
            }

            $this->sequence[] = $response;
        }

        return $this;
    }

    /**
     * Send a PSR-7 request and return a PSR-7 response.
     */
    public function sendRequest(PsrRequestInterface $request): PsrResponseInterface
    {
        $this->recordedRequests[] = $request;

        if (empty($this->sequence)) {
            throw new \RuntimeException('No more mock responses in the sequence. Did you forget to add mock responses?');
        }

        return array_shift($this->sequence);
    }

    /**
     * @return array<int, PsrRequestInterface>
     */
    public function getRecordedRequests(): array
    {
        return $this->recordedRequests;
    }

    public function getLastRequest(): ?PsrRequestInterface
    {
        return end($this->recordedRequests) ?: null;
    }

    public function createStream(string $contents): PsrStreamInterface
    {
        $stream = $this->createMock(PsrStreamInterface::class);
        $stream->method('getContents')->willReturn($contents);
        $stream->method('__toString')->willReturn($contents);
        $stream->method('rewind')->willReturnCallback(function () {});

        return $stream;
    }

    public function createResponse(int $statusCode, string $contents, array $headers = []): PsrResponseInterface
    {
        $stream = $this->createStream($contents);

        $response = $this->createMock(PsrResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getBody')->willReturn($stream);
        $response->method('getHeaders')->willReturn($headers);

        return $response;
    }

    public static function response(int $statusCode, array|string $contents, array $headers = []): PsrResponseInterface
    {
        $client = static::make();

        if (is_array($contents)) {
            $contents = json_encode($contents);
        }

        return $client->createResponse($statusCode, $contents, $headers);
    }

    /**
     * Create a response with server timestamp.
     */
    public static function responseServerTimestamp()
    {
        return static::response(200, (string) ((int) (microtime(true) * 1000)));
    }
}
