<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Support\Str;
use function EasyWeChat\Kernel\throw_if;

trait HasAttributes
{
    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @var bool
     */
    protected bool $snakeable = true;

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes = []): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute(string $attribute, mixed $value): static
    {
        Arr::set($this->attributes, $attribute, $value);

        return $this;
    }

    /**
     * @param string     $attribute
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getAttribute(string $attribute, mixed $default = null): mixed
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

    /**
     * @return array
     */
    public function getRequired(): array
    {
        return property_exists($this, 'required') ? $this->required ?? [] : [];
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function with(string $attribute, mixed $value): static
    {
        $this->snakeable && $attribute = Str::snake($attribute);

        $this->setAttribute($attribute, $value);

        return $this;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $attribute, mixed $value): static
    {
        $this->setAttribute($attribute, $value);

        return $this;
    }

    /**
     * @param string     $attribute
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $attribute, mixed $default = null): mixed
    {
        return $this->getAttribute($attribute, $default);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function has(string $attribute): bool
    {
        return Arr::has($this->attributes, $attribute);
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function merge(array $attributes): static
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * @param $keys
     *
     * @return array
     */
    public function only($keys): array
    {
        return Arr::only($this->attributes, $keys);
    }

    /**
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function all(): array
    {
        $this->assertRequiredAttributesExists();

        return $this->attributes;
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return $this
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): static
    {
        throw_if(
            0 !== stripos($method, 'with'),
            \BadMethodCallException::class,
            sprintf('Method "%s" does not exists.', $method)
        );

        return $this->with(substr($method, 4), array_shift($args));
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get(string $attribute): mixed
    {
        return $this->get($attribute);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     */
    public function __set(string $attribute, mixed $value): void
    {
        $this->with($attribute, $value);
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function __isset(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function assertRequiredAttributesExists()
    {
        foreach ($this->getRequired() as $attribute) {
            throw_if(
                is_null($this->get($attribute)),
                InvalidArgumentException::class,
                sprintf('"%s" cannot be empty.', $attribute)
            );
        }
    }
}
