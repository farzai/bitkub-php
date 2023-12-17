<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\CryptoEndpoint;
use Farzai\Bitkub\Tests\MockHttpClient;

it('should be crypto endpoint', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $user = $client->crypto();

    expect($user)->toBeInstanceOf(CryptoEndpoint::class);
});

it('can call addresses success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                [
                    'currency' => 'BTC',
                    'address' => '3BtxdKw6XSbneNvmJTLVHS9XfNYM7VAe8k',
                    'tag' => 0,
                    'time' => 1570893867,
                ],
            ],
            'pagination' => [
                'page' => 1,
                'last' => 1,
            ],
        ]));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $response = $client->crypto()->addresses([
        'p' => 1,
        'lmt' => 1,
    ])->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();
    expect($response->json('result.0'))->toBeArray();

    expect($response->json('result.0.currency'))->toBe('BTC');
    expect($response->json('result.0.address'))->toBe('3BtxdKw6XSbneNvmJTLVHS9XfNYM7VAe8k');
    expect($response->json('result.0.tag'))->toBe(0);
    expect($response->json('result.0.time'))->toBe(1570893867);

    expect($response->json('pagination'))->toBeArray();
    expect($response->json('pagination.page'))->toBe(1);
    expect($response->json('pagination.last'))->toBe(1);
});

it('can call withdrawal success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                'txid' => 'BTCWD0000012345',
                'adr' => '4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x',
                'mem' => '',
                'cur' => 'BTC',
                'amt' => 0.1,
                'fee' => 0.0002,
                'ts' => 1569999999,
            ],
        ]));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $response = $client->crypto()->withdrawal([
        'sym' => 'BTC',
        'amt' => 0.1,
        'adr' => '4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x',
        'mem' => '',
    ])->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();

    expect($response->json('result.txid'))->toBe('BTCWD0000012345');
    expect($response->json('result.adr'))->toBe('4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x');
    expect($response->json('result.mem'))->toBe('');
    expect($response->json('result.cur'))->toBe('BTC');
    expect($response->json('result.amt'))->toBe(0.1);
    expect($response->json('result.fee'))->toBe(0.0002);
    expect($response->json('result.ts'))->toBe(1569999999);
});
