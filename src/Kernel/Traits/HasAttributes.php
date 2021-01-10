<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Support\Str;

trait HasAttributes
{
    protected array $attributes = [];
    protected bool $snakeable = true;

    public function setAttributes(array $attributes = []): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function setAttribute(string $attribute, mixed $value): static
    {
        Arr::set($this->attributes, $attribute, $value);

        return $this;
    }

    public function getAttribute(string $attribute, mixed $default = null)
    {
        return Arr::get($this->attributes, $attribute, $default);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isRequired(string $attribute): bool
    {
        return in_array($attribute, $this->getRequired(), true);
    }

    public function getRequired(): array
    {
        return property_exists($this, 'required') ? $this->required ?? [] : [];
    }

    public function with(string $attribute, mixed $value): static
    {
        $this->snakeable && $attribute = Str::snake($attribute);

        $this->setAttribute($attribute, $value);

        return $this;
    }

    public function set(string $attribute, mixed $value): static
    {
        $this->setAttribute($attribute, $value);

        return $this;
    }

    public function get(string $attribute, mixed $default = null): mixed
    {
        return $this->getAttribute($attribute, $default);
    }

    public function has(string $attribute): bool
    {
        return Arr::has($this->attributes, $attribute);
    }

    public function merge(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function only($keys): array
    {
        return Arr::only($this->attributes, $keys);
    }

    public function all(): array
    {
        $this->assertRequiredAttributesExists();

        return $this->attributes;
    }

    public function __call(string $method, array $args)
    {
        if (0 === stripos($method, 'with')) {
            return $this->with(substr($method, 4), array_shift($args));
        }

        throw new \BadMethodCallException(sprintf('Method "%s" does not exists.', $method));
    }

    public function __get(string $attribute): mixed
    {
        return $this->get($attribute);
    }

    public function __set(string $attribute, mixed $value): void
    {
        $this->with($attribute, $value);
    }

    public function __isset(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    protected function assertRequiredAttributesExists()
    {
        foreach ($this->getRequired() as $attribute) {
            if (is_null($this->get($attribute))) {
                throw new InvalidArgumentException(sprintf('"%s" cannot be empty.', $attribute));
            }
        }
    }
}
