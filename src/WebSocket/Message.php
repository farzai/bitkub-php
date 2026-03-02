<?php

declare(strict_types=1);

namespace Farzai\Bitkub\WebSocket;

use ArrayAccess;
use DateTimeImmutable;
use Farzai\Support\Arr;

class Message implements \JsonSerializable, ArrayAccess
{
    private string $body;

    private ?array $jsonDecoded;

    private DateTimeImmutable $receivedAt;

    public function __construct(string $body, DateTimeImmutable $receivedAt)
    {
        $this->body = $body;
        $decoded = json_decode($body, true);
        $this->jsonDecoded = is_array($decoded) ? $decoded : null;
        $this->receivedAt = $receivedAt;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getReceivedAt(): DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function json($key = null)
    {
        if ($key === null) {
            return $this->jsonDecoded;
        }

        return Arr::get($this->jsonDecoded, $key);
    }

    public function __toString(): string
    {
        return $this->getBody();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return $this->jsonDecoded ?? [];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->jsonDecoded[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return Arr::get($this->jsonDecoded, $offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException('Message is immutable.');
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException('Message is immutable.');
    }

    public function __get($name)
    {
        return Arr::get($this->jsonDecoded, $name, null);
    }

    public function __isset($name): bool
    {
        return isset($this->jsonDecoded[$name]);
    }

    public function __set($name, $value): void
    {
        throw new \BadMethodCallException('Message is immutable.');
    }

    public function __unset($name): void
    {
        throw new \BadMethodCallException('Message is immutable.');
    }
}
