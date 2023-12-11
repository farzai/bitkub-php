<?php

use Farzai\Bitkub\Requests\GenerateSignatureV3;

it('can generate signature success', function () {
    $secret = 'test-secret';
    $timestamp = 1630483200000;

    $method = 'POST';
    $path = '/api/v3/market/balances';
    $query = '';
    $payload = '';

    $authorizer = new GenerateSignatureV3([
        'api_key' => 'test',
        'secret' => $secret,
    ]);

    $signature = $authorizer->generate($timestamp, $method, $path, $query, $payload);

    expect($signature)->toBe('b8403c345ce41b25b47885254fb8aeed9ad7ceb9e30425b86a9a151dd6ac2e35');
});
