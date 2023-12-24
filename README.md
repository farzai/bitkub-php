# Bitkub Wrapper - PHP (Unofficial)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/farzai/bitkub.svg?style=flat-square)](https://packagist.org/packages/farzai/bitkub)
[![Tests](https://img.shields.io/github/actions/workflow/status/farzai/bitkub-php/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/farzai/bitkub-php/actions/workflows/run-tests.yml)
[![codecov](https://codecov.io/gh/farzai/bitkub-php/branch/main/graph/badge.svg)](https://codecov.io/gh/farzai/bitkub-php)
[![Total Downloads](https://img.shields.io/packagist/dt/farzai/bitkub.svg?style=flat-square)](https://packagist.org/packages/farzai/bitkub)

Simplify the integration of the Bitkub API into your PHP application.
[Bitkub API Documentation](https://github.com/bitkub/bitkub-official-api-docs/blob/master/restful-api.md)

**Notes
We are not affiliated, associated, authorized, endorsed by, or in any way officially connected with Bitkub, or any of its subsidiaries or its affiliates.

## Installation

You can install the package via composer:

```bash
composer require farzai/bitkub
```

## Basic Usage

Restful API

```php
$bitkub = \Farzai\Bitkub\ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET_KEY')
    ->build();

// Basic usage
$market = $bitkub->market(); // Just call the market endpoint

// Get balances
$response = $market->balances();

// (Optional) You may call the `throw()` method to ensure that the response is successful
$response->throw();

// Get response data
$myBTC = $response->json('result.BTC.available');

echo "My BTC balance: {$myBTC}";
```

Websocket API

```php

$websocket = new \Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint(
    new \Farzai\Bitkub\WebSocketClient(
        \Farzai\Bitkub\ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET_KEY')
            ->build(),
    ),
);

$websocket->listen('trade.thb_ada', function (\Farzai\Bitkub\WebSocket\Message $message) {
    // Do something
    echo $message->json('sym').PHP_EOL;
});

// Or you can use multiple symbols like this
$websocket->listen(['trade.thb_ada', 'trade.thb_btc', function (\Farzai\Bitkub\WebSocket\Message $message) {
    // Do something
    echo $message->json('sym').PHP_EOL;
});

$websocket->run();
```


## Documentation

- [Bitkub Wrapper - PHP (Unofficial)](#bitkub-wrapper---php-unofficial)
  - [Installation](#installation)
  - [Basic Usage](#basic-usage)
  - [Documentation](#documentation)
    - [Market](#market)
      - [List all available symbols.](#list-all-available-symbols)
      - [Get the ticker for a specific symbol.](#get-the-ticker-for-a-specific-symbol)
      - [List recent trades.](#list-recent-trades)
      - [List open buy orders.](#list-open-buy-orders)
      - [List open sell orders.](#list-open-sell-orders)
      - [List all open orders.](#list-all-open-orders)
      - [Get user available balances](#get-user-available-balances)
      - [Create a buy order.](#create-a-buy-order)
      - [Create a sell order.](#create-a-sell-order)
      - [Cancel an open order.](#cancel-an-open-order)
      - [Get balances info: this includes both available and reserved balances.](#get-balances-info-this-includes-both-available-and-reserved-balances)
      - [List all open orders of the given symbol.](#list-all-open-orders-of-the-given-symbol)
      - [List all orders that have already matched.](#list-all-orders-that-have-already-matched)
      - [Get information regarding the specified order.](#get-information-regarding-the-specified-order)
    - [Crypto](#crypto)
      - [List all crypto addresses.](#list-all-crypto-addresses)
      - [Make a withdrawal to a trusted address.](#make-a-withdrawal-to-a-trusted-address)
      - [Make a withdraw to an internal address.](#make-a-withdraw-to-an-internal-address)
      - [List crypto deposit history.](#list-crypto-deposit-history)
      - [List crypto withdrawal history.](#list-crypto-withdrawal-history)
      - [Generate a new crypto address](#generate-a-new-crypto-address)
    - [System](#system)
      - [Get server status.](#get-server-status)
      - [Get server timestamp.](#get-server-timestamp)
    - [User](#user)
      - [Check trading credit balance.](#check-trading-credit-balance)
      - [Check deposit/withdraw limitations and usage.](#check-depositwithdraw-limitations-and-usage)
  - [Testing](#testing)
  - [Changelog](#changelog)
  - [Contributing](#contributing)
  - [Security Vulnerabilities](#security-vulnerabilities)
  - [Credits](#credits)
  - [License](#license)

### Market
Call the market endpoint. 
This will return an instance of `Farzai\Bitkub\Endpoints\MarketEndpoint` class.

```php
$market = $bitkub->market();

# Next, We will use this instance for the following examples below.
# ...
```

#### List all available symbols.
- GET `/api/market/symbols`

```php
$market->symbols();
```

#### Get the ticker for a specific symbol.
- GET `/api/market/ticker`

```php
$market->ticker(
    // string: The symbol.
    'THB_BTC'
);
```

#### List recent trades.
- GET `/api/market/trades`

```php
$market->trades([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### List open buy orders.
- GET `/api/market/bids`

```php
$market->bids([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### List open sell orders.
- GET `/api/market/asks`

```php
$market->asks([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### List all open orders.
- GET `/api/market/books`

```php
$market->books([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### Get user available balances
- GET `/api/market/wallet`
```php
$market->wallet();
```

#### Create a buy order.
- POST `/api/v3/market/place-bid`

```php
$market->placeBid([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // float: Amount you want to spend with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
    'amt' => 1000,

    // float: Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
    'rat' => 1000000,

    // string: Order type: limit or market (for market order, please specify rat as 0)
    'typ' => 'limit',

    // string: (Optional) your id for reference
    'client_id' => 'your_id',
]);
```

#### Create a sell order.
- POST `/api/v3/market/place-ask`

```php
$market->placeAsk([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // float: Amount you want to spend with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
    'amt' => 1000,

    // float: Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok)
    'rat' => 1000000,

    // string: Order type: limit or market (for market order, please specify rat as 0)
    'typ' => 'limit',

    // string: (Optional) your id for reference
    'client_id' => 'your_id',
]);
```

#### Cancel an open order.
- POST `/api/v3/market/cancel-order`

```php
$market->cancelOrder([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: The order ID.
    'id' => 123456,

    // string: The side of the order.
    'sd' => 'buy',

    // string: The hash of the order.
    'hash' => 'your_hash',
]);
```

#### Get balances info: this includes both available and reserved balances.
- POST `/api/v3/market/balances`
```php
$market->balances();
```

#### List all open orders of the given symbol.
- GET `/api/v3/market/my-open-orders`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
```php
$market->openOrders(
    // string: The symbol.
    'THB_BTC'
);
```

#### List all orders that have already matched.
- GET `/api/v3/market/my-order-history`

```php
$market->myOrderHistory([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: The page number.
    'p' => 1,

    // integer: Limit the number of results.
    'lmt' => 10,

    // integer: The start timestamp.
    'start' => 1614556800,

    // integer: The end timestamp.
    'end' => 1614643199,
]);
```


#### Get information regarding the specified order.
- GET `/api/v3/market/order-info`

```php
$market->myOrderInfo([
    // string: The symbol.
    'sym' => 'THB_BTC',

    // integer: The order ID.
    'id' => 123456,

    // string: The side of the order.
    'sd' => 'buy',

    // string: The hash of the order.
    'hash' => 'your_hash',
]);
```


### Crypto

Call the crypto endpoint.
This will return an instance of `Farzai\Bitkub\Endpoints\CryptoEndpoint` class.

```php
$crypto = $bitkub->crypto();

# Next, We will use this instance for the following examples below.
# ...
```

#### List all crypto addresses.
- GET `/api/v3/crypto/addresses`

```php
$crypto->addresses([
    // integer: The page number.
    'p' => 1,

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### Make a withdrawal to a trusted address.
- POST `/api/v3/crypto/withdraw`

```php
$crypto->withdrawal([
    // string: Currency for withdrawal (e.g. BTC, ETH)
    'cur' => 'BTC',
    
    // float: Amount you want to withdraw
    'amt' => 0.001,

    // string: Address to which you want to withdraw
    'adr' => 'your_address',

    // string: (Optional) Memo or destination tag to which you want to withdraw
    'mem' => 'your_memo',

    // string: Cryptocurrency network to withdraw
    'net' => 'BTC',
]);
```

#### Make a withdraw to an internal address.
- POST `/api/v3/crypto/internal-withdraw`

```php
$crypto->internalWithdrawal([
    // string: Currency for withdrawal (e.g. BTC, ETH)
    'cur' => 'BTC',

    // float: Amount you want to withdraw
    'amt' => 0.001,

    // string: Address to which you want to withdraw
    'adr' => 'your_address',

    // string: (Optional) Memo or destination tag to which you want to withdraw
    'mem' => 'your_memo',
]);
```


#### List crypto deposit history.
- POST `/api/v3/crypto/deposit-history`

```php
$crypto->depositHistory([
    // integer: The page number.
    'p' => 1,

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### List crypto withdrawal history.
- POST `/api/v3/crypto/withdrawal-history`

```php
$crypto->withdrawalHistory([
    // integer: The page number.
    'p' => 1,

    // integer: Limit the number of results.
    'lmt' => 10,
]);
```

#### Generate a new crypto address
- POST `/api/v3/crypto/generate-address`

```php
$crypto->generateAddress(
    // string Symbol (e.g. THB_BTC, THB_ETH, etc.)
    'THB_BTC'
);
```

### System

Call the system endpoint.
This will return an instance of `Farzai\Bitkub\Endpoints\SystemEndpoint` class.

```php
$system = $bitkub->system();

# Next, We will use this instance for the following examples below.
# ...
```

#### Get server status.
- GET `/api/status`

```php
$system->status();
```

#### Get server timestamp.
- GET `/api/v3/servertime`

```php
$system->serverTimestamp();
```

### User

Call the user endpoint.
This will return an instance of `Farzai\Bitkub\Endpoints\UserEndpoint` class.

```php
$user = $bitkub->user();

# Next, We will use this instance for the following examples below.
# ...
```

#### Check trading credit balance.
- POST `/api/v3/user/trading-credits`

```php
$user->tradingCredits();
```

#### Check deposit/withdraw limitations and usage.
- POST `/api/v3/user/limits`

```php
$user->limits();
```

---

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/farzai/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [parsilver](https://github.com/parsilver)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
