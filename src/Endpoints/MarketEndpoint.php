<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class MarketEndpoint extends AbstractEndpoint
{
    public function wallet(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/wallet')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }

    public function balances(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/balances')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }

    public function openOrders(string $sym): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('GET', '/api/v3/market/my-open-orders')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->withBody([
                'sym' => $sym,
            ])
            ->send();
    }
}
