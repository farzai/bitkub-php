<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\Client;

$client = new Client(config: [
    'api_key' => '',
    'secret' => '',
]);

// Get my balance
$response = $client->balances()->throw();
// $response = $client->wallet();
// $response = $client->openOrders('THB_BTC');
// $response = $client->userLimits();

echo 'My BTC balance: '.$response->json('result.BTC.available').PHP_EOL;
