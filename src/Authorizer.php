<?php

namespace Farzai\Bitkub;

class Authorizer
{
    /**
     * Generate the signature from the timestamp, the request method, API path, query parameter, and JSON payload using HMAC SHA-256.
     * Use the API Secret as the secret key for generating the HMAC variant of JSON payload.
     * The signature is in hex format.
     * The user has to attach the signature via the Request Header You must get a new timestamp in millisecond from /api/v3/servertime.
     * The old one is in second.
     */
    public function generateSignature(string $secretKey, int $timestamp, string $method, string $path, string $query = '', string $payload = ''): string
    {
        // Validate timestamp
        if (strlen((string) $timestamp) !== 13) {
            throw new \InvalidArgumentException('Timestamp must be in millisecond');
        }

        $method = strtoupper($method);
        $path = '/'.trim($path, '/');

        $message = sprintf(
            '%s%s%s%s%s',
            $timestamp,
            $method,
            $path,
            empty($query) ? '' : '?'.$query,
            $payload
        );

        return hash_hmac('sha256', $message, $secretKey);
    }
}
