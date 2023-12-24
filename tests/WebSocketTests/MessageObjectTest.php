<?php

use Farzai\Bitkub\WebSocket\Message;
use Farzai\Support\Carbon;

it('should return message object', function () {
    $body = json_encode($json = [
        'data' => [
            [
                121.82, // vol
                112510.1, // rate
                0.00108283, // amount
                0, // reserved, always 0
                false, // is new order
                false, // user is owner (deprecated)
            ],
        ],
        'pairing_id' => 1,
        'event' => 'bidschanged',
    ]);

    $currentDateTime = Carbon::now();

    $message = new Message($body, $currentDateTime->toDateTimeImmutable());

    expect($message->getBody())->toBe($body);
    expect($message->json())->toBe($json);

    expect((string) $message)->toBe($body);
    expect($message->toArray())->toBe($json);
    expect($message->offsetExists('data'))->toBeTrue();
    expect(json_encode($message))->toBe($body);

    expect($message->getReceivedAt()->format('Y-m-d H:i:s'))->toBe($currentDateTime->format('Y-m-d H:i:s'));

    expect($message->pairing_id)->toBe(1);
    expect($message->event)->toBe('bidschanged');
    expect($message['event'])->toBe('bidschanged');

    expect($message->json('data.0.0'))->toBe(121.82);

    // Test setter
    $message->event = 'askschanged';
    expect($message->event)->toBe('askschanged');

    $message['event'] = 'askschanged';
    expect($message->event)->toBe('askschanged');

    // Test unset
    expect(isset($message->event))->toBeTrue();

    unset($message->event);
    expect($message->event)->toBeNull();
    expect(isset($message->event))->toBeFalse();

    unset($message['pairing_id']);
    expect($message->pairing_id)->toBeNull();
});

it('should return null if json is not valid', function () {
    $body = 'invalid json';

    $currentDateTime = Carbon::now();

    $message = new Message($body, $currentDateTime->toDateTimeImmutable());

    expect($message->json())->toBeNull();
});
