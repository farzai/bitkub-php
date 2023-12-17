<?php

namespace Farzai\Bitkub;

use DateTimeImmutable;

class Utility
{
    /**
     * Generate signature.
     */
    public static function generateSignature($secret, $timestamp, $method, $path, $query, $payload)
    {
        $message = sprintf('%s%s%s%s%s', $timestamp, $method, $path, $query, $payload);
        $signature = hash_hmac('sha256', $message, $secret);

        return $signature;
    }

    /**
     * Get server timestamp.
     */
    public static function getServerTimestamp(Client $client): DateTimeImmutable
    {
        $timestamp = (int) $client->system()
            ->serverTimestamp()
            ->throw()->body();

        return \DateTimeImmutable::createFromFormat('U', $timestamp);
    }
}
