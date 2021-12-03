<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use EasyWeChat\Kernel\Contracts\Arrayable;
use IteratorAggregate;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable, Arrayable
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

    public function only(array $keys): Collection
    {
        $return = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if (!is_null($value)) {
                $return[$key] = $value;
            }
        }

        return new self($return);
    }

    public function except(array | int | string $keys): Collection
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return new self(Arr::except($this->items, $keys));
    }

    public function merge(array $items): Collection
    {
        $clone = new self($this->all());

        foreach ($items as $key => $value) {
            $clone->set($key, $value);
        }

        return $clone;
    }

    public function has(string | int $key): bool
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

    public function add(string | int $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }

    public function set(string | int $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }

    public function get(string | int $key, mixed $default = null): mixed
    {
        return Arr::get($this->items, $key, $default);
    }

    public function forget(string | int $key): void
    {
        Arr::forget($this->items, $key);
    }

    #[Pure]
    public function toArray(): array
    {
        return $this->all();
    }

    public function toJson(int $option = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->all(), $option);
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function __serialize(): array
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function __unserialize($serialized)
    {
        $this->items = $serialized;
    }

    public function __get(string | int $key): mixed
    {
        return $this->get($key);
    }

    public function __set(string | int $key, mixed $value)
    {
        $this->set($key, $value);
    }

    public function __isset(string | int $key): bool
    {
        return $this->has($key);
    }

    public function __unset(string | int $key)
    {
        $this->forget($key);
    }

    public static function __set_state(array $properties)
    {
        return (new self($properties));
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($this->offsetExists($offset)) {
            $this->forget($offset);
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ? $this->get($offset) : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }
}
