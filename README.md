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

## Usage

```php
use Farzai\Bitkub\ClientBuilder;

$bitkub = ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET_KEY')
    ->build();

// Basic usage
$market = $bitkub->market(); // Just call the market endpoint

// Get balances
$myBTC = $market->balances()
    ->throw()
    ->json('result.BTC.available');

echo "My BTC balance: {$myBTC}";
```


Or you can manage your logic with `Response` object

```php

$market = $bitkub->market();

$response = $market->balances();

// Check response result
if ($response->isSuccessful()) {
    // @example
    // [
    //     "error" => 0,
    //     "result" => [
    //         "BTC" => [
    //             "available" => 0,
    //             "reserved" => 0,
    //         ],
    //         "ETH" => [//...],
    //         "ADA" => [//...],
    //     ],
    // ]

    // Get response data
    $jsonData = $response->json(); // @return array

    // Or
    echo $response->json('result.BTC.available');
}
```

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
