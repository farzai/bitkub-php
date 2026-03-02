<?php

declare(strict_types=1);

namespace Farzai\Bitkub;

use Phrity\Net\Uri;
use Psr\Http\Message\UriInterface;

class UriFactory
{
    public static function createFromUri(string $uri): UriInterface
    {
        return new Uri($uri);
    }
}
