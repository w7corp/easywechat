<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use EasyWeChat\Kernel\Contracts\Arrayable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Serializable, Arrayable
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function all(): array
    {
        return $this->items;
    }

    public function only(array $keys): static
    {
        $return = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if (!is_null($value)) {
                $return[$key] = $value;
            }
        }

        return new static($return);
    }

    public function except(array|int|string $keys): static
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new static(Arr::except($this->items, $keys));
    }

    public function merge(array $items): static
    {
        $clone = new static($this->all());

        foreach ($items as $key => $value) {
            $clone->set($key, $value);
        }

        return $clone;
    }

    public function has(string|int $key): bool
    {
        return !is_null(Arr::get($this->items, $key));
    }

    public function first(): mixed
    {
        return reset($this->items);
    }

    public function last(): mixed
    {
        $end = end($this->items);

        reset($this->items);

        return $end;
    }

    public function add(string|int $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }

    public function set(string|int $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }

    public function get(string|int $key, mixed $default = null): mixed
    {
        return Arr::get($this->items, $key, $default);
    }

    public function forget(string|int $key)
    {
        Arr::forget($this->items, $key);
    }

    public function toArray(): array
    {
        return $this->all();
    }

    public function toJson($option = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->all(), $option);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }

    public function serialize(): string
    {
        return serialize($this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }

    public function __get($key): mixed
    {
        return $this->get($key);
    }

    public function __set(string|int $key, mixed $value)
    {
        $this->set($key, $value);
    }

    public function __isset(string|int $key): bool
    {
        return $this->has($key);
    }

    public function __unset(string|int $key)
    {
        $this->forget($key);
    }

    public static function __set_state(array $properties)
    {
        return (new static($properties))->all();
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->forget($offset);
        }
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}
