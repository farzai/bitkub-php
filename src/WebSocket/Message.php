<?php

namespace Farzai\Bitkub\WebSocket;

use ArrayAccess;
use DateTimeImmutable;
use Farzai\Support\Arr;

class Message implements \JsonSerializable, ArrayAccess
{
    private string $body;

    private $jsonDecoded;

    private DateTimeImmutable $receivedAt;

    public function __construct(string $body, DateTimeImmutable $receivedAt)
    {
        $this->body = $body;
        $this->jsonDecoded = @json_decode($body, true) ?? false;
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
            return $this->jsonDecoded ?: null;
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
        return $this->jsonDecoded;
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
        $this->jsonDecoded[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->jsonDecoded[$offset]);
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
        $this->jsonDecoded[$name] = $value;
    }

    public function __unset($name): void
    {
        unset($this->jsonDecoded[$name]);
    }
}
