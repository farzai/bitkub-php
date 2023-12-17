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
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }

    /**
     * Check deposit/withdraw limitations and usage.
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "limits": { // limitations by kyc level
     *              "crypto": {
     *                  "deposit": 0.88971929, // BTC value equivalent
     *                  "withdraw": 0.88971929 // BTC value equivalent
     *              },
     *              "fiat": {
     *                  "deposit": 200000, // THB value equivalent
     *                  "withdraw": 200000 // THB value equivalent
     *              }
     *          },
     *          "usage": { // today's usage
     *              "crypto": {
     *                  "deposit": 0, // BTC value equivalent
     *                  "withdraw": 0, // BTC value equivalent
     *                  "deposit_percentage": 0,
     *                  "withdraw_percentage": 0,
     *                  "deposit_thb_equivalent": 0, // THB value equivalent
     *                  "withdraw_thb_equivalent": 0 // THB value equivalent
     *              },
     *              "fiat": {
     *                  "deposit": 0, // THB value equivalent
     *                  "withdraw": 0, // THB value equivalent
     *                  "deposit_percentage": 0,
     *                  "withdraw_percentage": 0
     *              }
     *          },
     *          "rate": 224790 // current THB rate used to calculate
     *      }
     * }
     */
    public function userLimits(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/user/limits')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }
}
