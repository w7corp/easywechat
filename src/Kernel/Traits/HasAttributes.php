<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

trait HasAttributes
{
    protected array $attributes = [];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return \json_encode($this->attributes);
    }

    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    public function __set(string $attribute, mixed $value)
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function offsetExists(string $offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet(string $offset): mixed
    {
        return $this->attributes[$offset];
    }

    public function offsetSet(string $offset, mixed $value)
    {
        if (null === $offset) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetUnset(string $offset)
    {
        unset($this->attributes[$offset]);
    }
}
