<?php

use Farzai\Bitkub\UriFactory;

it('can create uri', function () {
    $uri = UriFactory::createFromUri('https://api.bitkub.com');

    expect($uri->getScheme())->toBe('https');
    expect($uri->getHost())->toBe('api.bitkub.com');
    expect($uri->getPath())->toBe('');
});
