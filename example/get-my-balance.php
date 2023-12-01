<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\Client;

$client = new Client(config: [
    'api_key' => '',
    'secret' => '',
]);

// Get my balance
$response = $client->getBalances();
// $response = $client->getWallet();
// $response = $client->getOpenOrders('THB_BTC');
// $response = $client->getUserLimits();

dd($response->json());
