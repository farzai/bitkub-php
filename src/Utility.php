<?php

declare(strict_types=1);

namespace Farzai\Bitkub;

class Utility
{
    /**
     * Generate signature.
     */
    public static function generateSignature(
        string $secret,
        int $timestamp,
        string $method,
        string $path,
        string $query,
        string $payload
    ): string {
        $message = sprintf('%s%s%s%s%s', $timestamp, $method, $path, $query, $payload);

        return hash_hmac('sha256', $message, $secret);
    }
}
