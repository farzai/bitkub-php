<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class UserEndpoint extends AbstractEndpoint
{
    /**
     * Check trading credit balance.
     *
     * @response
     * {
     *      "error": 0,
     *      "result": 100
     * }
     */
    public function tradingCredits(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/user/trading-credits')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }

    public function userLimits(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/user/limits')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }
}
