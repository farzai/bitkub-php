<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\ClientBuilder;

$client = ClientBuilder::create()
    ->setCredentials('2f45daf1214fe9bbcd45995d91d63b6cf998f6d84be58768abb59c94815eb0ef', 'd2465d55764140c7b82c8106cb641007f0a674a33079494cabcabdff483d5982qUVyglzHi1BpKCaVf8CqouDrMbqi')
    ->build();

$market = $client->market();

// Get my balance
$response = $market->balances()->throw();

echo 'My BTC balance: '.$response->json('result.BTC.available').PHP_EOL;
