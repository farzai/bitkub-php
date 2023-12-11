<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class CryptoEndpoint extends AbstractEndpoint
{
    /**
     * List all crypto addresses.
     *
     * @param  array<{
     *     p: int,
     *    lmt: int,
     * }> $params
     *
     * @response
     * {
     *      "error":0,
     *      "result": [
     *          {
     *              "currency": "BTC",
     *              "address": "3BtxdKw6XSbneNvmJTLVHS9XfNYM7VAe8k",
     *              "tag": 0,
     *              "time": 1570893867
     *          }
     *      ],
     *      "pagination": {
     *          "page": 1,
     *          "last": 1
     *      }
     * }
     */
    public function addresses(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('GET', '/api/v3/crypto/addresses')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->withBody($params)
            ->send();
    }
}
