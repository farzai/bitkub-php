<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Requests\PendingRequest;

abstract class AbstractEndpoint
{
    protected ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    protected function makeRequest(string $method, string $path, array $options = []): PendingRequest
    {
        return new PendingRequest($this->client, $method, $path, $options);
    }
}
