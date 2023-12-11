<?php

namespace Farzai\Bitkub\Contracts;

use Farzai\Transport\Transport;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Log\LoggerInterface;

interface ClientInterface
{
    /**
     * Get the transport instance.
     */
    public function getTransport(): Transport;

    /**
     * Get the config.
     */
    public function getConfig(): array;

    /**
     * Get the PSR-3 logger
     */
    public function getLogger(): LoggerInterface;

    /**
     * Send the request.
     */
    public function sendRequest(PsrRequestInterface $request);
}
