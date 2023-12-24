<?php

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
    ) {
    }

    public function handle(array $listeners): void
    {
        $events = $this->getEventNames($listeners);

        $this->logger->info('[WebSocket] - Connecting to WebSocket server...');

        $this->logger->debug('[WebSocket] - Events: '.implode(', ', $events));

        $client = new WebSocketClient('wss://api.bitkub.com/websocket-api/'.implode(',', $events));

        $client
            ->addMiddleware(new WebSocketMiddleware\CloseHandler())
            ->addMiddleware(new WebSocketMiddleware\PingResponder());

        $client->onText(function (WebSocketClient $client, WebSocketConnection $connection, WebSocketMessage $message) use ($listeners) {
            $receivedAt = Carbon::now();

            $data = @json_decode($message->getContent(), true) ?? [];
            if (! isset($data['stream'])) {
                $this->logger->warning('[WebSocket] - Unknown data: '.$message->getContent());

                return;
            }

            $event = $data['stream'];
            if (! isset($listeners[$event])) {
                $this->logger->warning('[WebSocket] - Unknown event: '.$event);

                return;
            }

            $message = new Message(
                $message->getContent(),
                $receivedAt->toDateTimeImmutable(),
            );

            foreach ($listeners[$event] as $listener) {
                $this->logger->info('[WebSocket] - Event: '.$event);

                $listener($message);
            }
        });

        $client->onClose(function () {
            $this->logger->info('[WebSocket] - Connection closed.');
        });

        $client->start();
    }

    private function getEventNames(array $listeners): array
    {
        return array_unique(array_keys($listeners));
    }
}
