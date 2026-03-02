<?php

declare(strict_types=1);

use Farzai\Bitkub\UriFactory;

it('can create uri', function () {
    $uri = UriFactory::createFromUri('https://api.bitkub.com');

    expect($uri->getScheme())->toBe('https');
    expect($uri->getHost())->toBe('api.bitkub.com');
    expect($uri->getPath())->toBe('');
});

it('can create uri with path, query, and port', function () {
    $uri = UriFactory::createFromUri('https://api.bitkub.com:8443/api/v3/market?sym=THB_BTC');

    expect($uri->getScheme())->toBe('https');
    expect($uri->getHost())->toBe('api.bitkub.com');
    expect($uri->getPort())->toBe(8443);
    expect($uri->getPath())->toBe('/api/v3/market');
    expect($uri->getQuery())->toBe('sym=THB_BTC');
});

it('can create uri with fragment', function () {
    $uri = UriFactory::createFromUri('https://api.bitkub.com/docs#section');

    expect($uri->getPath())->toBe('/docs');
    expect($uri->getFragment())->toBe('section');
});

it('can create uri with http scheme', function () {
    $uri = UriFactory::createFromUri('http://localhost:3000/api');

    expect($uri->getScheme())->toBe('http');
    expect($uri->getHost())->toBe('localhost');
    expect($uri->getPort())->toBe(3000);
});
