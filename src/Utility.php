<?php

namespace Farzai\Bitkub;

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
}
