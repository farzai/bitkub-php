<?php

require_once __DIR__.'/../vendor/autoload.php';

use Farzai\Bitkub\Client;

$client = new Client(
    // You can pass your own PSR-18 client
);

// Get my balance
$response = $client->getBalances();

dd($response->json());
