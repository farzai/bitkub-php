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

```php
use Farzai\Bitkub\ClientBuilder;

$bitkub = ClientBuilder::create()
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

---


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

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |

```php
$market->ticker('THB_BTC');
```

#### List recent trades.
- GET `/api/market/trades`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| lmt | integer | Limit the number of results. |
```php
$market->trades([
    'sym' => 'THB_BTC',
    'lmt' => 10,
]);
```

#### List open buy orders.
- GET `/api/market/bids`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| lmt | integer | Limit the number of results. |
```php
$market->bids([
    'sym' => 'THB_BTC',
    'lmt' => 10,
]);
```

#### List open sell orders.
- GET `/api/market/asks`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| lmt | integer | Limit the number of results. |
```php
$market->asks([
    'sym' => 'THB_BTC',
    'lmt' => 10,
]);
```

#### List all open orders.
- GET `/api/market/books`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| lmt | integer | Limit the number of results. |
```php
$market->books([
    'sym' => 'THB_BTC',
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

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. Please note that the current endpoint requires the symbol thb_btc. However, it will be changed to btc_thb soon and you will need to update the configurations accordingly for uninterrupted API functionality. |
| amt | float | Amount you want to spend with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok) |
| rat | float | Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok) |
| typ | string | Order type: limit or market (for market order, please specify rat as 0) |
| client_id | string | your id for reference ( not required ) |
```php
$market->placeBid([
    'sym' => 'THB_BTC',
    'amt' => 1000,
    'rat' => 1000000,
    'typ' => 'limit',
    'client_id' => 'your_id',
]);
```

#### Create a sell order.
- POST `/api/v3/market/place-ask`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| amt | float | Amount you want to spend with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok) |
| rat | float | Rate you want for the order with no trailing zero (e.g. 1000.00 is invalid, 1000 is ok) |
| typ | string | Order type: limit or market (for market order, please specify rat as 0) |
| client_id | string | your id for reference ( not required ) |
```php
$market->placeAsk([
    'sym' => 'THB_BTC',
    'amt' => 1000,
    'rat' => 1000000,
    'typ' => 'limit',
    'client_id' => 'your_id',
]);
```

#### Cancel an open order.
- POST `/api/v3/market/cancel-order`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| id | integer | The order ID. |
| sd | string | The side of the order. |
| hash | string | The hash of the order. |
```php
$market->cancelOrder([
    'sym' => 'THB_BTC',
    'id' => 123456,
    'sd' => 'buy',
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
$market->openOrders('THB_BTC');
```

#### List all orders that have already matched.
- GET `/api/v3/market/my-order-history`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| p | integer | The page number. |
| lmt | integer | Limit the number of results. |
| start | integer | The start timestamp. |
| end | integer | The end timestamp. |

```php
$market->myOrderHistory([
    'sym' => 'THB_BTC',
    'p' => 1,
    'lmt' => 10,
    'start' => 1614556800,
    'end' => 1614643199,
]);
```


#### Get information regarding the specified order.
- GET `/api/v3/market/order-info`

| Parameter | Type | Description |
| --- | --- | --- |
| sym | string | The symbol. |
| id | integer | The order ID. |
| sd | string | The side of the order. |
| hash | string | The hash of the order. |
```php
$market->myOrderInfo([
    'sym' => 'THB_BTC',
    'id' => 123456,
    'sd' => 'buy',
    'hash' => 'your_hash',
]);
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
