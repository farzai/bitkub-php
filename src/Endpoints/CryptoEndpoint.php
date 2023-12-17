<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class CryptoEndpoint extends AbstractEndpoint
{
    /**
     * List all crypto addresses.
     *
     * p int Page (optional)
     * lmt int Limit (optional)
     *
     * @param  array<{
     *      p: int,
     *      lmt: int,
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
            ->withQuery(array_filter($params))
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }

    /**
     * Make a withdrawal to a trusted address.
     *
     * cur string Currency for withdrawal (e.g. BTC, ETH)
     * amt float Amount you want to withdraw
     * adr string Address to which you want to withdraw
     * mem string (Optional) Memo or destination tag to which you want to withdraw
     * net string Cryptocurrency network to withdraw
     * No default value of this field. Please find the available network from the link as follows. https://www.bitkub.com/fee/cryptocurrency
     *
     * For example ETH refers to ERC-20.
     * For request on ERC-20, please assign the net value as ETH.
     * For request on BEP-20, please assign the net value as BSC.
     * For request on KAP-20, please assign the net value as BKC.
     *
     * @param  array<{
     *      cur: string,
     *      amt: float,
     *      adr: string,
     *      mem: string
     *      net: string,
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "txn": "BTCWD0000012345", // local transaction id
     *          "adr": "4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x", // address
     *          "mem": "", // memo
     *          "cur": "BTC", // currency
     *          "amt": 0.1, // withdraw amount
     *          "fee": 0.0002, // withdraw fee
     *          "ts": 1569999999 // timestamp
     *     }
     * }
     */
    public function withdrawal(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/crypto/withdraw')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->withBody($params)
            ->send();
    }

    /**
     * Make a withdraw to an internal address.
     * The destination address is not required to be a trusted address.
     * This API is not enabled by default, Only KYB users can request this feature by contacting us via support@bitkub.com
     *
     * cur string Currency for withdrawal (e.g. BTC, ETH)
     * amt float Amount you want to withdraw
     * adr string Address to which you want to withdraw
     * mem string (Optional) Memo or destination tag to which you want to withdraw
     *
     * @param  array<{
     *      cur: string,
     *      amt: float,
     *      adr: string,
     *      mem: string
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "txn": "BTCWD0000012345", // local transaction id
     *          "adr": "4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x", // address
     *          "mem": "", // memo
     *          "cur": "BTC", // currency
     *          "amt": 0.1, // withdraw amount
     *          "fee": 0.0002, // withdraw fee
     *          "ts": 1569999999 // timestamp
     *      }
     * }
     */
    public function internalWithdrawal(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/crypto/internal-withdraw')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->withBody($params)
            ->send();
    }

    /**
     * List crypto deposit history.
     *
     * p int Page (optional)
     * lmt int Limit (optional)
     *
     * @param  array<{
     *      p: int,
     *      lmt: int,
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": [
     *          {
     *              "hash": "XRPWD0000100276",
     *              "currency": "XRP",
     *              "amount": 5.75111474,
     *              "from_address": "sender address",
     *              "to_address": "recipient address",
     *              "confirmations": 1,
     *              "status": "complete",
     *              "time": 1570893867
     *          }
     *      ],
     *      "pagination": {
     *          "page": 1,
     *          "last": 1
     *      }
     * }
     */
    public function depositHistory(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/crypto/deposit-history')
            ->withQuery(array_filter($params))
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }

    /**
     * List crypto withdrawal history.
     *
     * p int Page (optional)
     * lmt int Limit (optional)
     *
     * @param  array<{
     *      p: int,
     *      lmt: int,
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": [
     *          {
     *              "txn_id": "XRPWD0000100276",
     *              "hash": "send_internal",
     *              "currency": "XRP",
     *              "amount": "5.75111474",
     *              "fee": 0.01,
     *              "address": "rpXTzCuXtjiPDFysxq8uNmtZBe9Xo97JbW",
     *              "status": "complete",
     *              "time": 1570893493
     *          }
     *      ],
     *      "pagination": {
     *          "page": 1,
     *          "last": 1
     *      }
     * }
     */
    public function withdrawalHistory(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/crypto/withdrawal-history')
            ->withQuery(array_filter($params))
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }

    /**
     * Generate a new crypto address (will replace existing address; previous address can still be used to received funds).
     *
     * sym string Symbol (e.g. THB_BTC, THB_ETH, etc.)
     *
     *
     * @response
     * {
     *      "error": 0,
     *      "result": [
     *          {
     *              "currency": "ETH",
     *              "address": "0x520165471daa570ab632dd504c6af257bd36edfb",
     *              "memo": ""
     *          }
     *      ]
     * }
     */
    public function generateAddress(string $symbol): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/crypto/generate-address')
            ->withQuery(['sym' => $symbol])
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config, $this->client))
            ->send();
    }
}
