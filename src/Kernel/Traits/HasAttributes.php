<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

trait HasAttributes
{
    /**
     * @var  array<int|string,mixed>  $attributes
     */
    protected array $attributes = [];

    /**
     * @param  array<int|string,mixed>  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return \json_encode($this->attributes) ?: '';
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->attributes);
    }

    /**
     * @param  array<int|string,mixed>  $attributes
     */
    public function merge(array $attributes): static
    {
        $this->attributes = \array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @return  array<int|string,mixed>  $attributes
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    public function __set(string $attribute, mixed $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    public function __get(string $attribute): mixed
    {
        return $this->attributes[$attribute] ?? null;
    }

    /**
     * @param  mixed  $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * @param  mixed  $offset
     *
     * @return bool
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->attributes[$offset];
    }

    /**
     * @param  mixed  $offset
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null === $offset) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    /**
     * @param  mixed  $offset
     *
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }
}
