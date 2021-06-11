<?php

namespace EasyWeChat\Kernel\Contracts;

interface Config
{
    public function all(): array;
    public function has(string $key): bool;
    public function set(array | string $key, mixed $value = null);
    public function get(array | string $key, mixed $default = null): mixed;
    public function prepend(string $key, mixed $value);
    public function push(string $key, mixed $value);
}
