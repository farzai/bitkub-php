<?php

namespace Farzai\Bitkub\Contracts;

interface WebSocketEngineInterface
{
    public function addListener(string $event, callable $listener);

    public function run(): void;
}
