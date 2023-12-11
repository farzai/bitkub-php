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
     *
     * @response
     * 1701251212273
     */
    public function serverTimestamp(): ResponseInterface
    {
        return $this->makeRequest('GET', '/api/v3/servertime')->send();
    }
}
