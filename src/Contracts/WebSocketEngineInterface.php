<?php

declare(strict_types=1);

namespace Farzai\Bitkub\Contracts;

interface WebSocketEngineInterface
{
    /**
     * Handle WebSocket connections and dispatch messages to listeners.
     *
     * @param  array<string, array<callable(\Farzai\Bitkub\WebSocket\Message): void>>  $listeners
     */
    public function handle(array $listeners): void;
}
