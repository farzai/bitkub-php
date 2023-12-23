<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint;
use Farzai\Bitkub\WebSocket\Message;
use Farzai\Bitkub\WebSocketClient;

$client = ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
    ->build();

$websocket = new MarketEndpoint(new WebSocketClient($client));

$websocket->listen('trade.thb_ada', function (Message $message) {
    echo $message->json('sym').PHP_EOL;
});

$websocket->run();
