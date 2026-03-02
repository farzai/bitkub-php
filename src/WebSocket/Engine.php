<?php

declare(strict_types=1);

namespace Farzai\Bitkub\WebSocket;

use Farzai\Bitkub\Contracts\WebSocketEngineInterface;
use Farzai\Support\Carbon;
use Psr\Log\LoggerInterface;
use WebSocket\Client as WebSocketClient;
use WebSocket\Connection as WebSocketConnection;
use WebSocket\Message\Message as WebSocketMessage;
use WebSocket\Middleware as WebSocketMiddleware;

class Engine implements WebSocketEngineInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private string $baseUrl = 'wss://api.bitkub.com/websocket-api/',
        private int $reconnectAttempts = 3,
        private int $reconnectDelayMs = 1000,
    ) {}

    public function handle(array $listeners): void
    {
        $events = $this->getEventNames($listeners);
        $url = rtrim($this->baseUrl, '/').'/'.implode(',', $events);

        $attempt = 0;

        do {
            if ($attempt > 0) {
                $this->logger->info('[WebSocket] - Reconnecting (attempt '.$attempt.' of '.$this->reconnectAttempts.')...');
                usleep($this->reconnectDelayMs * 1000);
            }

            $this->logger->info('[WebSocket] - Connecting to WebSocket server...');
            $this->logger->debug('[WebSocket] - Events: '.implode(', ', $events));

            $client = new WebSocketClient($url);

            $client
                ->addMiddleware(new WebSocketMiddleware\CloseHandler)
                ->addMiddleware(new WebSocketMiddleware\PingResponder);

            $client->onText(function (WebSocketClient $client, WebSocketConnection $connection, WebSocketMessage $message) use ($listeners) {
                $receivedAt = Carbon::now();

                $data = json_decode($message->getContent(), true);
                if (! is_array($data) || ! isset($data['stream'])) {
                    $this->logger->warning('[WebSocket] - Received non-JSON or unknown message format.');

                    return;
                }

                $event = $data['stream'];
                if (! isset($listeners[$event])) {
                    $this->logger->warning('[WebSocket] - Unknown event: '.$event);

                    return;
                }

                $wsMessage = new Message(
                    $message->getContent(),
                    $receivedAt->toDateTimeImmutable(),
                    $data,
                );

                foreach ($listeners[$event] as $listener) {
                    $this->logger->debug('[WebSocket] - Event: '.$event);

                    try {
                        $listener($wsMessage);
                    } catch (\Throwable $e) {
                        $this->logger->error('[WebSocket] - Listener error on event '.$event.': '.$e->getMessage());
                    }
                }
            });

            $client->onClose(function () {
                $this->logger->info('[WebSocket] - Connection closed.');
            });

            try {
                $client->start();
                break; // Clean close — no reconnect needed
            } catch (\Throwable $e) {
                $this->logger->error('[WebSocket] - Connection error: '.$e->getMessage());
            }

            $attempt++;
        } while ($attempt <= $this->reconnectAttempts);
    }

    private function getEventNames(array $listeners): array
    {
        return array_unique(array_keys($listeners));
    }
}
