<?php

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\Config as ConfigContract;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\Arr;

class Config implements \ArrayAccess, ConfigContract
{
    public function __construct(
        protected array $items = [],
    ) {}

    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }

    public function get(array|string $key, mixed $default = null): mixed
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return Arr::get($this->items, $key, $default);
    }

    public function getMany(array $keys): array
    {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }

            $config[$key] = Arr::get($this->items, $key, $default);
        }

        return $config;
    }

    public function set(array|string $key, mixed $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
    }

    public function prepend(string $key, mixed $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    public function push(string $key, mixed $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function requiredVerify(array $keys = []): bool
    {
        if (!$keys) {
            return true;
        }

        $error = "";

        foreach ($keys as $key) {
            if (!$this->has($key)) {
                $error .= sprintf("\"%s\" cannot be empty.\r\n", $key);
            }
        }

        throw_if(
            !empty($error),
            InvalidArgumentException::class,
            $error,
        );

        return true;
    }

    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    public function offsetSet($key, mixed $value)
    {
        $this->set($key, $value);
    }

    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
