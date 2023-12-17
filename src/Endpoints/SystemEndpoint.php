<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Transport\Contracts\ResponseInterface;

class SystemEndpoint extends AbstractEndpoint
{
    /**
     * Get server status.
     *
     * @response
     * [
     *      {
     *          "name":"Non-secure endpoints",
     *          "status":"ok",
     *          "message":""
     *      },
     *      {
     *          "name":"Secure endpoints",
     *          "status":"ok",
     *          "message":""
     *      }
     * ]
     */
    public function status(): ResponseInterface
    {
        return $this->makeRequest('GET', '/api/status')->send();
    }

    /**
     * Get server timestamp.
     *
     * @response
     * 1702793384662
     */
    public function serverTimestamp(): ResponseInterface
    {
        return $this->makeRequest('GET', '/api/v3/servertime')->send();
    }
}
