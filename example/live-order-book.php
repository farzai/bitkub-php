<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\WebSocket\Endpoints\LiveOrderBookEndpoint;
use Farzai\Bitkub\WebSocket\Message;
use Farzai\Bitkub\WebSocketClient;

$client = ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
    ->build();

$websocket = new LiveOrderBookEndpoint(new WebSocketClient($client));

$websocket->listen('thb_btc', function (Message $message) {
    echo $message->json('event').PHP_EOL;
});

$websocket->run();
