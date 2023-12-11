<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Client;
use Farzai\Bitkub\Requests\PendingRequest;

abstract class AbstractEndpoint
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function makeRequest(string $method, string $path, array $options = []): PendingRequest
    {
        return new PendingRequest($this->client, $method, $path, $options);
    }
}
