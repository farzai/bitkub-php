<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\WebSocket\Endpoints\LiveOrderBookEndpoint;
use Farzai\Bitkub\WebSocket\Message;
use Farzai\Bitkub\WebSocketClient;

$websocket = new LiveOrderBookEndpoint(
    new WebSocketClient(
        ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    )
);

$websocket->listen('THB_ADA', function (Message $message) {
    echo $message->json('event').PHP_EOL;
});

$websocket->run();
