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
    expect($response->json('result.0.tag'))->toBe(0);
    expect($response->json('result.0.time'))->toBe(1570893867);

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

    expect($response->json('result.mem'))->toBe('');
    expect($response->json('result.cur'))->toBe('BTC');
    expect($response->json('result.amt'))->toBe(0.1);
    expect($response->json('result.fee'))->toBe(0.0002);
    expect($response->json('result.ts'))->toBe(1569999999);
});

it('can call internalWithdrawal success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                'txn' => 'BTCWD0000012345',
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

    $response = $client->crypto()->internalWithdrawal([
        'sym' => 'BTC',
        'amt' => 0.1,
        'adr' => '4asyjKw6XScneNvhJTLVHS9XfNYM7VBf8x',
        'mem' => '',
    ])->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();

    expect($response->json('result.txn'))->toBe('BTCWD0000012345');
    expect($response->json('result.mem'))->toBe('');
    expect($response->json('result.cur'))->toBe('BTC');
    expect($response->json('result.fee'))->toBe(0.0002);
    expect($response->json('result.ts'))->toBe(1569999999);
});

it('can call depositHistory success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                [
                    'hash' => 'XRPWD0000100276',
                    'currency' => 'XRP',
                    'amount' => 5.75111474,
                    'from_address' => 'sender address',
                    'to_address' => 'recipient address',
                    'confirmations' => 1,
                    'status' => 'complete',
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

    $response = $client->crypto()->depositHistory([
        'p' => 1,
        'lmt' => 1,
    ])->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();
    expect($response->json('result.0'))->toBeArray();

    expect($response->json('result.0.hash'))->toBe('XRPWD0000100276');
    expect($response->json('result.0.time'))->toBe(1570893867);

    expect($response->json('pagination.last'))->toBe(1);
});

it('can call withdrawalHistory success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                [
                    'txn_id' => 'XRPWD0000100276',
                    'hash' => 'send_internal',
                    'currency' => 'XRP',
                    'amount' => 5.75111474,
                    'fee' => 0.01,
                    'address' => 'rpXTzCuXtjiPDFysxq8uNmtZBe9Xo97JbW',
                    'status' => 'complete',
                    'time' => 1570893493,
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

    $response = $client->crypto()->withdrawalHistory([
        'p' => 1,
        'lmt' => 1,
    ])->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();
    expect($response->json('result.0'))->toBeArray();

    expect($response->json('result.0.txn_id'))->toBe('XRPWD0000100276');
});

it('can call generateAddress success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                'currency' => 'ETH',
                'address' => '0x520165471daa570ab632dd504c6af257bd36edfb',
                'memo' => '',
            ],
        ]));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $response = $client->crypto()->generateAddress('THB_ETH')->throw();

    expect($response->getStatusCode())->toBe(200);
    expect($response->json('result'))->toBeArray();

    expect($response->json('result.currency'))->toBe('ETH');
});
