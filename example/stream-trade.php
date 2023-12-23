<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;
use Farzai\Bitkub\WebSocket\Message;
use Farzai\Bitkub\WebSocketClient;

$client = ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
    ->build();

$websocket = new WebSocketClient($client);

$websocket->listen([
    'market.trade.thb_btc' => [
        function (Message $message) {
            echo $message->json('sym').PHP_EOL;
        },
    ],
]);

$websocket->listen([
    'market.trade.thb_eth' => function (Message $message) {
        echo $message->json('sym').PHP_EOL;
    },
]);

$websocket->listen('market.trade.thb_ada', function (Message $message) {
    echo $message->json('sym').PHP_EOL;
});

$websocket->run();
