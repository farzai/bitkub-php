<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Transport\Contracts\ResponseInterface;

class SystemEndpoint extends AbstractEndpoint
{
    public function status(): ResponseInterface
    {
        return $this->makeRequest('GET', '/api/status')->send();
    }

    /**
     * Get server timestamp.
     */
    public function serverTimestamp(): ResponseInterface
    {
        return $this->makeRequest('GET', '/api/servertime')->send();
    }
}
