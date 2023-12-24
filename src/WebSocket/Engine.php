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
        $events = array_unique(array_keys($listeners));

        $this->logger->info('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Connecting to WebSocket server...');

        $this->logger->debug('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Events: '.implode(', ', $events));

        $client = new WebSocketClient('wss://api.bitkub.com/websocket-api/'.implode(',', $events));

        $client
            ->addMiddleware(new WebSocketMiddleware\CloseHandler())
            ->addMiddleware(new WebSocketMiddleware\PingResponder());

        $client->onText(function (WebSocketClient $client, WebSocketConnection $connection, WebSocketMessage $message) use ($listeners) {
            $receivedAt = Carbon::now();

            $data = @json_decode($message->getContent(), true) ?? [];
            if (! isset($data['stream'])) {
                $this->logger->warning('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Unknown data: '.$message->getContent());

                return;
            }

            $event = $data['stream'];
            if (! isset($listeners[$event])) {
                $this->logger->warning('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Unknown event: '.$event);

                return;
            }

            $message = new Message(
                $message->getContent(),
                $receivedAt->toDateTimeImmutable(),
            );

            foreach ($listeners[$event] as $listener) {
                $this->logger->info('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Event: '.$event);

                $listener($message);
            }
        });

        $client->onClose(function () {
            $this->logger->info('[WebSocket] - '.Carbon::now()->format('Y-m-d H:i:s').' - Connection closed.');
        });

        $client->start();
    }
}
