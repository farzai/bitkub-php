<?php

declare(strict_types=1);

namespace Farzai\Bitkub;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Transport\Transport;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Log\LoggerInterface;

class Client implements ClientInterface
{
    /**
     * The config.
     *
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * The transport instance.
     */
    private Transport $transport;

    /**
     * The logger instance.
     */
    private LoggerInterface $logger;

    /**
     * Create a new client instance.
     */
    public function __construct(
        array $config,
        Transport $transport,
        LoggerInterface $logger,
    ) {
        $this->config = $config;
        $this->transport = $transport;
        $this->logger = $logger;
    }

    private ?Endpoints\MarketEndpoint $marketEndpoint = null;

    private ?Endpoints\CryptoEndpoint $cryptoEndpoint = null;

    private ?Endpoints\UserEndpoint $userEndpoint = null;

    private ?Endpoints\SystemEndpoint $systemEndpoint = null;

    public function market(): Endpoints\MarketEndpoint
    {
        return $this->marketEndpoint ??= new Endpoints\MarketEndpoint($this);
    }

    public function crypto(): Endpoints\CryptoEndpoint
    {
        return $this->cryptoEndpoint ??= new Endpoints\CryptoEndpoint($this);
    }

    public function user(): Endpoints\UserEndpoint
    {
        return $this->userEndpoint ??= new Endpoints\UserEndpoint($this);
    }

    public function system(): Endpoints\SystemEndpoint
    {
        return $this->systemEndpoint ??= new Endpoints\SystemEndpoint($this);
    }

    public function getTransport(): Transport
    {
        return $this->transport;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Send the request.
     */
    public function sendRequest(PsrRequestInterface $request): PsrResponseInterface
    {
        return $this->transport->sendRequest($request);
    }
}
