<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class UserEndpoint extends AbstractEndpoint
{
    public function userLimits(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/user/limits')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }
}
