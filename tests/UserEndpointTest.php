<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\UserEndpoint;
use Farzai\Bitkub\Tests\MockHttpClient;

it('should be user endpoint', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $user = $client->user();

    expect($user)->toBeInstanceOf(UserEndpoint::class);
});

it('can call tradingCredits success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => 100,
        ]));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $response = $client->user()->tradingCredits()->throw();

    expect($response->json('result'))->toBe(100);
});

it('can call userLimits success', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn () => MockHttpClient::responseServerTimestamp())
        ->addSequence(fn () => MockHttpClient::response(200, [
            'error' => 0,
            'result' => [
                'limits' => [
                    'crypto' => [
                        'deposit' => 0.88971929,
                        'withdraw' => 0.88971929,
                    ],
                    'fiat' => [
                        'deposit' => 200000,
                        'withdraw' => 200000,
                    ],
                ],
                'usage' => [
                    'crypto' => [
                        'deposit' => 0,
                        'withdraw' => 0,
                        'deposit_percentage' => 0,
                        'withdraw_percentage' => 0,
                        'deposit_thb_equivalent' => 0,
                        'withdraw_thb_equivalent' => 0,
                    ],
                    'fiat' => [
                        'deposit' => 0,
                        'withdraw' => 0,
                        'deposit_percentage' => 0,
                        'withdraw_percentage' => 0,
                    ],
                ],
                'rate' => 224790,
            ],
        ]));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $response = $client->user()->limits()->throw();

    expect($response->json('result'))->toBe([
        'limits' => [
            'crypto' => [
                'deposit' => 0.88971929,
                'withdraw' => 0.88971929,
            ],
            'fiat' => [
                'deposit' => 200000,
                'withdraw' => 200000,
            ],
        ],
        'usage' => [
            'crypto' => [
                'deposit' => 0,
                'withdraw' => 0,
                'deposit_percentage' => 0,
                'withdraw_percentage' => 0,
                'deposit_thb_equivalent' => 0,
                'withdraw_thb_equivalent' => 0,
            ],
            'fiat' => [
                'deposit' => 0,
                'withdraw' => 0,
                'deposit_percentage' => 0,
                'withdraw_percentage' => 0,
            ],
        ],
        'rate' => 224790,
    ]);
});
