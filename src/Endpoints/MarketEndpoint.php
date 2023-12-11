<?php

namespace Farzai\Bitkub\Endpoints;

use Farzai\Bitkub\Requests\GenerateSignatureV3;
use Farzai\Transport\Contracts\ResponseInterface;

class MarketEndpoint extends AbstractEndpoint
{
    /**
     * Get user available balances
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "THB": {
     *              "available": 1000,
     *              "reserved": 0
     *          },
     *          "BTC": {
     *              // ...
     *          },
     *      }
     * }
     */
    public function wallet(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/wallet')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }

    /**
     * Create a buy order.
     *
     * sym string The symbol. Please note that the current endpoint requires the symbol thb_btc. However, it will be changed to btc_thb soon and you will need to update the configurations accordingly for uninterrupted API functionality.
     * amt float Amount you want to spend with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
     * rat float Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
     * typ string Order type: limit or market (for market order, please specify rat as 0)
     * client_id string your id for reference ( not required )
     *
     * @param array<{
     *      sym: string,
     *      amt: string,
     *      rat: string,
     *      typ: string,
     *      client_id: string,
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "id": "1", // order id
     *          "hash": "fwQ6dnQWQPs4cbatF5Am2xCDP1J", // order hash
     *          "typ": "limit", // order type
     *          "amt": 1000, // spending amount
     *          "rat": 15000, // rate
     *          "fee": 2.5, // fee
     *          "cre": 2.5, // fee credit used
     *          "rec": 0.06666666, // amount to receive
     *          "ts": 1533834547 // timestamp
     *          "ci": "input_client_id" // input id for reference
     *      }
     * }
     */
    public function placeBid(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/place-bid')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->withBody($params)
            ->send();
    }

    /**
     * Create a sell order.
     *
     * sym string The symbol. Please note that the current endpoint requires the symbol thb_btc. However, it will be changed to btc_thb soon and you will need to update the configurations accordingly for uninterrupted API functionality.
     * amt float Amount you want to sell with no trailing zero (e.g. 0.10000000 is invalid, 0.1 is ok)
     * rat float Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
     * typ string Order type: limit or market (for market order, please specify rat as 0)
     * client_id string your id for reference ( not required )
     *
     * @param array<{
     *      sym: string,
     *      amt: string,
     *      rat: string,
     *      typ: string,
     *      client_id: string,
     * }> $params
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "id": "1", // order id
     *          "hash": "fwQ6dnQWQPs4cbatF5Am2xCDP1J", // order hash
     *          "typ": "limit", // order type
     *          "amt": 1.000000, // selling amount
     *          "rat": 15000, // rate
     *          "fee": 0.0025, // fee
     *          "cre": 0.0025, // fee credit used
     *          "rec": 15000, // amount to receive
     *          "ts": 1533834547 // timestamp
     *          "ci": "input_client_id" // input id for reference
     *     }
     * }
     */
    public function placeAsk(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/place-ask')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->withBody($params)
            ->send();
    }

    /**
     * Cancel an open order.
     *
     * sym string The symbol. Please note that the current endpoint requires the symbol thb_btc. However, it will be changed to btc_thb soon and you will need to update the configurations accordingly for uninterrupted API functionality.
     * id string Order id you wish to cancel
     * sd string Order side: buy or sell
     * hash string Cancel an order with order hash (optional). You don't need to specify sym, id, and sd when you specify order hash.
     *
     * @param array<{
     *      sym: string,
     *      id: string,
     *      sd: string,
     *      hash: string,
     * }> $params
     *
     * @response
     * {
     *     "error": 0
     * }
     */
    public function cancelOrder(array $params): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/cancel-order')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->withBody($params)
            ->send();
    }

    /**
     * Get balances info: this includes both available and reserved balances.
     *
     * @response
     * {
     *      "error": 0,
     *      "result": {
     *          "THB": {
     *              "available": 1000,
     *              "reserved": 0
     *          },
     *          "BTC": {
     *              // ...
     *          },
     *      }
     * }
     */
    public function balances(): ResponseInterface
    {
        $config = $this->client->getConfig();

        return $this->makeRequest('POST', '/api/v3/market/balances')
            ->acceptJson()
            ->withInterceptor(new GenerateSignatureV3($config))
            ->send();
    }

    /**
     * List all open orders of the given symbol.
     * Note : The client_id of this API response is the input body field name client_id , was inputted by the user of APIs.
     *
     * @param  string  $sym The symbol (e.g. btc_thb)
     *
     * @response
     * {
     *      "error": 0,
     *      "result": [
     *          {
     *               "id": "2", // order id
     *               "hash": "fwQ6dnQWQPs4cbatFSJpMCcKTFR", // order hash
     *               "side": "SELL", // order side
     *               "type": "limit", // order type
     *               "rate": 15000, // rate
     *               "fee": 35.01, // fee
     *               "credit": 35.01, // credit used
     *               "amount": 0.93333334, // amount
     *               "receive": 14000, // amount to receive
     *               "parent_id": 1, // parent order id
     *               "super_id": 1, // super parent order id
     *               "client_id": "client_id" // client id
     *               "ts": 1533834844 // timestamp
     *          }
     *          // ...
     *      ]
     * }
     */
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
