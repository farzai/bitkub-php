<?php

declare(strict_types=1);

namespace Farzai\Bitkub\Tests\WebSocketTests;

use Farzai\Bitkub\Contracts\WebSocketEngineInterface;
use Farzai\Bitkub\WebSocket\Message;
use Farzai\Support\Carbon;

class MockWebSocketEngine implements WebSocketEngineInterface
{
    /** @var array<int, array{stream: string, data: array<string, mixed>}> */
    private array $messages = [];

    private bool $handled = false;

    /**
     * @param  array<string, mixed>  $data
     */
    public function addMessage(string $stream, array $data): self
    {
        $this->messages[] = ['stream' => $stream, 'data' => $data];

        return $this;
    }

    public function handle(array $listeners): void
    {
        $this->handled = true;

        foreach ($this->messages as $queued) {
            $stream = $queued['stream'];
            if (! isset($listeners[$stream])) {
                continue;
            }

            $payload = array_merge($queued['data'], ['stream' => $stream]);
            $body = json_encode($payload);
            $message = new Message($body, Carbon::now()->toDateTimeImmutable(), $payload);

            foreach ($listeners[$stream] as $listener) {
                $listener($message);
            }
        }
    }

    public function wasHandled(): bool
    {
        return $this->handled;
    }
}
