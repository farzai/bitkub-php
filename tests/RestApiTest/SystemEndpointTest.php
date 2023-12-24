<?php

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\Endpoints\SystemEndpoint;
use Farzai\Bitkub\Tests\MockHttpClient;

it('should be system endpoint', function () {
    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->build();

    $system = $client->system();

    expect($system)->toBeInstanceOf(SystemEndpoint::class);
});

it('can get status of server', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, json_encode([
            [
                'name' => 'Non-secure endpoints',
                'status' => 'ok',
                'message' => '',
            ],
            [
                'name' => 'Secure endpoints',
                'status' => 'ok',
                'message' => '',
            ],
        ])));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $system = $client->system();

    expect($system->status()->json())->toBe([
        [
            'name' => 'Non-secure endpoints',
            'status' => 'ok',
            'message' => '',
        ],
        [
            'name' => 'Secure endpoints',
            'status' => 'ok',
            'message' => '',
        ],
    ]);
});

it('can get server timestamp', function () {
    $psrClient = MockHttpClient::make()
        ->addSequence(fn ($client) => $client->createResponse(200, '1702793384662'));

    $client = ClientBuilder::create()
        ->setCredentials('test', 'secret')
        ->setHttpClient($psrClient)
        ->build();

    $system = $client->system();

    expect((int) $system->serverTimestamp()->body())->toBe(1702793384662);
});
