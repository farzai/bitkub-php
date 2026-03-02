<?php

declare(strict_types=1);

namespace Farzai\Bitkub\Contracts;

interface WebSocketEngineInterface
{
    public function handle(array $listeners): void;
}
