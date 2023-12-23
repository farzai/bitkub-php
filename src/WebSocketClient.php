<?php

namespace Farzai\Bitkub;

use Farzai\Bitkub\Contracts\ClientInterface;
use Farzai\Bitkub\Responses\Message;
use Farzai\Transport\Transport;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Log\LoggerInterface;

final class WebSocketClient implements ClientInterface
{
    private ClientInterface $client;

    private Contracts\WebSocketEngineInterface $websocket;

    /**
     * @var array<string, array<mixed>>
     */
    private array $listeners = [];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        $this->websocket = new WebSocket\Engine($this->getLogger());
    }

    public function getConfig(): array
    {
        return $this->client->getConfig();
    }

    public function getTransport(): Transport
    {
        return $this->client->getTransport();
    }

    public function getLogger(): LoggerInterface
    {
        return $this->client->getLogger();
    }

    public function sendRequest(PsrRequestInterface $request)
    {
        return $this->client->sendRequest($request);
    }

    /**
     * Add event listener.
     *
     * @example $websocket->listen('market.trade.thb_btc', function (Message $message) {
     *    echo $message->json('sym').PHP_EOL;
     * });
     *
     * @param  array<string, callable|array<callable>>|string  $listeners
     */
    public function listen($listeners)
    {
        if (func_num_args() === 2) {
            $eventName = func_get_arg(0);
            $listener = is_array(func_get_arg(1)) ? func_get_arg(1) : [func_get_arg(1)];

            $listeners = [$eventName => $listener];
        }

        foreach ($listeners as $event => $listener) {
            if (! isset($this->listeners[$event])) {
                $this->listeners[$event] = [];
            }

            if (is_callable($listener)) {
                $this->listeners[$event][] = $listener;
            } elseif (is_array($listener)) {
                foreach ($listener as $callback) {
                    $this->listeners[$event][] = $callback;
                }
            }
        }

        return $this;
    }

    public function run()
    {
        foreach ($this->listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->websocket->addListener($event, $listener);
            }
        }

        $this->websocket->run();
    }
}
