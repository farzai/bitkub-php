<?php

namespace Farzai\Bitkub\Contracts;

interface WebSocketEngineInterface
{
    public function handle(array $listeners): void;
}
