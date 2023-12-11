<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;

$client = ClientBuilder::create()
    ->setCredentials('YOUR_API_KEY', 'YOUR_SECRET_KEY')
    ->build();

$market = $client->market();

// Get my balance
$response = $market->balances()->throw();

echo 'My BTC balance: '.$response->json('result.BTC.available').PHP_EOL;
