<?php

require_once __DIR__.'/../vendor/autoload.php';

$websocket = new \Farzai\Bitkub\WebSocket\Endpoints\MarketEndpoint(
    new \Farzai\Bitkub\WebSocketClient(
        \Farzai\Bitkub\ClientBuilder::create()
            ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET')
            ->build()
    )
);

$websocket->listen('trade.thb_ada', function (Farzai\Bitkub\WebSocket\Message $message) {
    echo $message->json('sym').PHP_EOL;
});

$websocket->run();
